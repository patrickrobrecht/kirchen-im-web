<?php

namespace KirchenImWeb\Helpers;

use PDO;
use PDOException;

/**
 * Class Database
 *
 * @package KirchenImWeb\Helpers
 */
class Database extends AbstractHelper
{
    private $connection;

    protected function __construct()
    {
        parent::__construct();
        $options  = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS
        ];
        try {
            $this->connection = new PDO(
                'mysql:host=' . DATABASE_HOSTNAME . ';dbname=' . DATABASE_NAME . ';charset=utf8',
                DATABASE_USERNAME,
                DATABASE_PASSWORD,
                $options
            );
        } catch (PDOException $e) {
            echo '<h1>Datenbank-Verbindung fehlgeschlagen</h1>';
        }
    }

    public function getEntries(): array
    {
        $websites = Configuration::getInstance()->websites;
        $query = 'SELECT id, slug, lat, lon, name, street, postalCode, city, country, denomination, churches.type';
        foreach ($websites as $websiteId => $websiteName) {
            $query .= ', ' . $websiteId . '.url AS ' . $websiteId;
        }
        $query .= ' FROM churches ';
        foreach ($websites as $websiteId => $websiteName) {
            $query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.churchId = churches.id
                AND ' . $websiteId . '.type = "' . $websiteId . '" ';
        }
        $statement = $this->connection->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredEntries($filters, $websites, $compare = false): array
    {
        // Query churches
        $query = 'SELECT id, slug, lat, lon, name, street, postalCode, city, country, denomination, churches.type';
        foreach ($websites as $websiteId => $websiteName) {
            $query .= ', ' . $websiteId . '.url AS ' . $websiteId . ', '
                      . $websiteId . '.followers AS ' . $websiteId . '_followers';
        }
        $query .= ' FROM churches ';

        // .... get the URLs to show.
        foreach ($websites as $websiteId => $websiteName) {
            $query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.churchId = churches.id
								AND ' . $websiteId . '.type = "' . $websiteId . '" ';
        }

        // ... and apply the filters
        $conditions = [];
        if (isset($filters['ids']) && $filters['ids'] && count($filters['ids']) > 0) {
            $conditions[] = 'id IN (' . implode(', ', $filters['ids']) . ') ';
        }
        if (isset($filters['parent']) && $filters['parent'] > 0) {
            $children = $this->getChildrenOfEntry(
                $filters['parent'],
                self::getOption($filters, 'childrenRecursive')
            );
            $childrenIds = array_merge(
                self::getOption($filters, 'includeSelf') ? [$filters['parent']] : [],
                self::extractIds($children)
            );
            $conditions[] = 'id IN (' . implode(', ', $childrenIds) . ') ';
        }
        if (isset($filters['name']) && $filters['name'] !== '') {
            $conditions[] = 'name LIKE :name ';
        }
        if (isset($filters['postalCode']) && $filters['postalCode'] != 0) {
            $conditions[] = 'postalCode = :postalCode ';
        }
        if (isset($filters['city']) && $filters['city'] !== '') {
            $conditions[] = 'city LIKE :city ';
        }
        if (isset($filters['country']) && $filters['country'] !== '') {
            $conditions[] = 'country = :country ';
        }
        if (isset($filters['denomination']) && $filters['denomination'] !== '') {
            $conditions[] = 'denomination = :denomination ';
        }
        if (isset($filters['type']) && $filters['type'] !== '') {
            $conditions[] = 'churches.type = :ctype ';
        }
        if (isset($filters['hasWebsiteType']) && $filters['hasWebsiteType'] !== '') {
            $conditions[] = 'EXISTS (SELECT * FROM websites WHERE id = churchId AND type = :wtype) ';
        }
        if ($compare) {
            // restrict to churches with at least one profile with followers set
            $compare_conditions = [];
            foreach (Configuration::getInstance()->networksToCompare as $websiteId => $websiteName) {
                $compare_conditions[] =
                    '(' . $websiteId . '.followers IS NOT NULL AND ' . $websiteId . '.followers > 0) ';
            }
            if (sizeof($compare_conditions) > 0) {
                $only_socialmedia_compare_condition = '';
                foreach ($compare_conditions as $condition) {
                    $only_socialmedia_compare_condition .= ' OR ' . $condition;
                }
                $only_socialmedia_compare_condition =
                    preg_replace('/ OR/', '(', $only_socialmedia_compare_condition, 1) . ')';
                $conditions[] = $only_socialmedia_compare_condition;
            }
        }

        if (sizeof($conditions) > 0) {
            $whereclause = '';
            foreach ($conditions as $condition) {
                $whereclause .= ' AND ' . $condition;
            }
            $whereclause = preg_replace('/ AND/', ' WHERE', $whereclause, 1);
            $query .= $whereclause;
            $query .= 'ORDER BY id';
        } else {
            $query .= 'ORDER BY churches.timestamp DESC, id DESC LIMIT 25';
        }

        $statement = $this->connection->prepare($query);
        if (isset($filters['name']) && $filters['name'] !== '') {
            $name = '%' . $filters['name'] . '%';
            $statement->bindParam(':name', $name);
        }
        if (isset($filters['postalCode']) && $filters['postalCode'] != 0) {
            $statement->bindParam(':postalCode', $filters['postalCode']);
        }
        if (isset($filters['city']) && $filters['city'] !== '') {
            $city = '%' . $filters['city'] . '%';
            $statement->bindParam(':city', $city);
        }
        if (isset($filters['country']) && $filters['country'] !== '') {
            $statement->bindParam(':country', $filters['country']);
        }
        if (isset($filters['denomination']) && $filters['denomination'] !== '') {
            $statement->bindParam(':denomination', $filters['denomination']);
        }
        if (isset($filters['type']) && $filters['type'] !== '') {
            $statement->bindParam(':ctype', $filters['type']);
        }
        if (isset($filters['hasWebsiteType']) && $filters['hasWebsiteType'] !== '') {
            $statement->bindParam(':wtype', $filters['hasWebsiteType']);
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function getOption($filters, $name)
    {
        return $filters['options'][$name] ?? false;
    }

    private static function extractIds($entries)
    {
        $ids = [];
        foreach ($entries as $entry) {
            $ids[] = $entry['id'];
            if (isset($entry['children'])) {
                $ids = array_merge($ids, Database::extractIds($entry['children']));
            }
        }
        return $ids;
    }

    public function getWebsitesWithMissingFollowers(): array
    {
        $statement = $this->connection->query('SELECT *
            FROM websites
            WHERE followersStatus = 0
            ORDER BY type, url');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getErrorWebsitesByStatusCode(): array
    {
        $statement = $this->connection->query('SELECT *
            FROM websites
            WHERE statusCode != 200 AND notesUpdate < (NOW() - INTERVAL 90 DAY)
            ORDER BY url');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentlyAddedEntries(): array
    {
        $showWebsites = Configuration::getInstance()->preselectedWebsites;

        $query = 'SELECT id, slug, name, postalCode, city, country, denomination, churches.type';
        foreach ($showWebsites as $websiteId => $websiteName) {
            $query .= ', ' . $websiteId . '.url AS ' . $websiteId;
        }
        $query .= ' FROM churches ';

        // .... get the URLs to show.
        foreach ($showWebsites as $websiteId => $websiteName) {
            $query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.churchId = churches.id 
								AND ' . $websiteId . '.type = "' . $websiteId . '" ';
        }

        $query .= ' ORDER BY churches.timestamp DESC, id DESC LIMIT 6';

        $statement = $this->connection->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParentEntries(): array
    {
        $statement = $this->connection->query('SELECT id, slug, name FROM churches
			WHERE hasChildren = 1
			ORDER BY name');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEntry($id, $childrenRecursive = false)
    {
        $id = (int)$id;

        // Query for the entry itself
        $statement = $this->connection->prepare('SELECT * FROM churches
            WHERE churches.id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        // Query for the children
        $data['children'] = $this->getChildrenOfEntry($id, $childrenRecursive);

        // Query for the parents
        $data['parents'] = $this->getParentsOfEntry($data['parentId'], []);

        // Query for the websites
        $statement = $this->connection->prepare('SELECT websiteId, url, type, followers FROM websites
            WHERE churchId = :id
            ORDER BY type');
        $statement->bindParam(':id', $id);
        $statement->execute();
        $data['websites'] = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Queries for the followers history of the social media entries.
        foreach ($data['websites'] as $key => $website) {
            if (array_key_exists($website['type'], Configuration::getInstance()->networksToCompare)) {
                $data['websites'][$key]['followerHistory'] = $this->getFollowerHistory($website['websiteId']);
            }
            unset($data['websites'][$key]['websiteId']);
        }

        return $data;
    }

    private function getParentsOfEntry($parentId, $parents)
    {
        if ($parentId === null) {
            return $parents;
        }
        $statement = $this->connection->prepare('SELECT id, slug, name, parentId
            FROM churches
            WHERE id = :parentId');
        $statement->bindParam(':parentId', $parentId);
        $statement->execute();
        $parentData = $statement->fetch(PDO::FETCH_ASSOC);
        $parents[] = [
            'id'   => $parentId,
            'slug' => $parentData['slug'],
            'name' => $parentData['name']
        ];
        return $this->getParentsOfEntry($parentData['parentId'], $parents);
    }

    private function getChildrenOfEntry($parentId, $recursive): array
    {
        $statement = $this->connection->prepare('SELECT id, slug, name
            FROM churches
            WHERE parentId = :parentId
            ORDER BY name');
        $statement->bindParam(':parentId', $parentId);
        $statement->execute();
        $children = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($recursive) {
            foreach ($children as $key => $value) {
                $children[$key]['children'] = $this->getChildrenOfEntry($value{'id'}, true);
            }
        }
        return $children;
    }

    public function getAllChurchesWithLastUpdate(): array
    {
        $statement = $this->connection->query('SELECT id, slug, IFNULL(lastFollowerUpdate, timestamp) AS lastUpdate
            FROM churches
			LEFT JOIN (
				SELECT churchId, MAX(followersLastUpdate) AS lastFollowerUpdate FROM websites
				WHERE followersLastUpdate IS NOT NULL
				GROUP BY churchId
			) AS w ON id = w.churchId');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getURLsForUpdate(int $limit): array
    {
        $networksToCompareList = $this->getNetworksToCompareList();
        $statement = $this->connection->prepare('SELECT websiteId, churchId, url, type, followers, followersLastUpdate
            FROM websites
            WHERE type IN (' . $networksToCompareList . ') AND followersStatus != 2
            ORDER BY followersLastUpdate
            LIMIT :maxResults');
        $statement->bindParam(':maxResults', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWebsitesToCheck(int $limit): array
    {
        $networksToCompareList = $this->getNetworksToCompareList();
        $statement = $this->connection->prepare('SELECT websiteId, url
            FROM websites
            WHERE lastCheck IS NULL OR lastCheck <= CURDATE() AND type NOT IN (' . $networksToCompareList . ')
            ORDER BY lastCheck
            LIMIT :maxResults');
        $statement->bindParam(':maxResults', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getNetworksToCompareList(): string
    {
        $networksToCompare = Configuration::getInstance()->networksToCompare;
        $networksToCompareAsStrings = [];
        foreach ($networksToCompare as $type => $typeName) {
            $networksToCompareAsStrings[] = "'" . $type . "'";
        }
        return implode(', ', $networksToCompareAsStrings);
    }

    public function updateWebsiteCheck($websiteId, $statusCode, $redirectTarget): bool
    {
        $statement = $this->connection->prepare('UPDATE websites
            SET statusCode = :statusCode,
                redirectTarget = :redirectTarget,
                lastCheck = NOW()
            WHERE websiteId = :websiteId');
        $statement->bindParam(':statusCode', $statusCode);
        $statement->bindParam(':redirectTarget', $redirectTarget);
        $statement->bindParam(':websiteId', $websiteId);
        return $statement->execute();
    }

    public function updateFollowers($websiteId, $followers): bool
    {
        if ($followers === false) {
            $statement = $this->connection->prepare('UPDATE websites
                SET followersStatus = 0,
                    followersLastUpdate = NOW()
                WHERE websiteId = :websiteId');
        } else {
            $statement = $this->connection->prepare('UPDATE websites 
			    SET followers = :followers,
			        followersStatus = 1,
			        followersLastUpdate = NOW()
			    WHERE websiteId = :websiteId');
            $statement->bindParam(':followers', $followers, PDO::PARAM_INT);
        }
        $statement->bindParam(':websiteId', $websiteId);
        return $statement->execute();
    }

    public function addFollowers($websiteId, $followers): bool
    {
        $statement = $this->connection->prepare('INSERT INTO followers 
			    (websiteId, followers, date)
			    VALUES (:websiteId, :followers, NOW())');
        $statement->bindParam(':websiteId', $websiteId);
        $statement->bindParam(':followers', $followers, PDO::PARAM_INT);
        return $statement->execute();
    }

    private function getFollowerHistory($websiteId): array
    {
        $statement = $this->connection->prepare('SELECT followers, date FROM followers
			WHERE websiteId = :websiteId');
        $statement->bindParam(':websiteId', $websiteId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount()
    {
        $queryTotal = 'SELECT count(*) AS count FROM churches';
        $statementTotal = $this->connection->query($queryTotal);
        return $statementTotal->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatsByCountry(): array
    {
        $queryByCountry = 'SELECT count(*) AS count, country as countryCode
            FROM churches
            GROUP BY country
            ORDER BY count DESC';
        $statementByCountry = $this->connection->query($queryByCountry);
        return $statementByCountry->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsByDenomination(): array
    {
        $queryByDenomination = 'SELECT count(*) AS count, denomination FROM churches GROUP BY denomination';
        $statementByDenomination = $this->connection->query($queryByDenomination);
        return $statementByDenomination->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsByType(): array
    {
        $queryByType = 'SELECT count(*) AS count, type FROM churches GROUP BY type ORDER BY count DESC';
        $statementByType = $this->connection->query($queryByType);
        return $statementByType->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsByWebsite(): array
    {
        $queryByWebsite = 'SELECT count(*) AS count, type FROM websites GROUP BY type ORDER BY count DESC';
        $statementByWebsite = $this->connection->query($queryByWebsite);
        return $statementByWebsite->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsHTTPS(): array
    {
        $queryByWebsiteHTTPS = 'SELECT w1.type, IFNULL(count, 0) as count, IFNULL(countHTTPS, 0) as countHTTPS  
            FROM (
                SELECT count(*) AS count, type FROM websites
                WHERE type IN ("web", "rss", "blog") GROUP BY type
            ) as w1
            LEFT JOIN (
                SELECT count(*) AS countHTTPS, type FROM websites
                WHERE url LIKE "https://%" AND type IN ("web", "rss", "blog") GROUP BY type
            ) as w2 
            ON w1.type = w2.type
            ORDER BY count DESC';
        $statementByWebsiteHTTPS = $this->connection->query($queryByWebsiteHTTPS);
        return $statementByWebsiteHTTPS->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Adds a church.
     *
     * @param array $data the church data
     *
     * @return number the id of the created church.
     */
    public function addChurch($data)
    {
        $urls = $data['urls'];

        // Add church to the database.
        $statement = $this->connection->prepare('
			INSERT INTO churches (slug, name, street, postalCode, city, country,
			                      lat, lon, denomination, type, hasChildren, timestamp)
			VALUES (:slug, :name, :street, :postalCode, :city, :countryCode,
			        :lat, :lon, :denomination, :type, :hasChildren, NOW())');

        $slug = $this->createSlug($data['name']);

        $statement->bindParam(':slug', $slug);
        $statement->bindParam(':name', $data['name']);
        $statement->bindParam(':street', $data['street']);
        $statement->bindParam(':postalCode', $data['postalCode']);
        $statement->bindParam(':city', $data['city']);
        $statement->bindParam(':countryCode', $data['countryCode']);
        $statement->bindParam(':lat', $data['lat']);
        $statement->bindParam(':lon', $data['lon']);
        $statement->bindParam(':denomination', $data['denomination']);
        $statement->bindParam(':type', $data['type']);
        $statement->bindParam(':hasChildren', $data['hasChildren'], PDO::PARAM_INT);
        $statement->execute();

        $churchId = $this->connection->lastInsertId();

        // Set parent id.
        if ($data['parentId'] !== 0) {
            $statement = $this->connection->prepare('UPDATE churches SET parentId = :parentId
				WHERE id = :id');
            $statement->bindParam(':parentId', $data['parentId'], PDO::PARAM_INT);
            $statement->bindParam(':id', $churchId, PDO::PARAM_INT);
            $statement->execute();
        }

        // Add the URLs to the database.
        $statement = $this->connection->prepare('INSERT INTO websites (churchId, type, url) 
			VALUES (:churchId, :type, :url)');
        foreach ($urls as $type => $url) {
            $statement->bindParam(':churchId', $churchId);
            $statement->bindParam(':type', $type);
            $statement->bindParam(':url', $url);
            $statement->execute();
        }

        return $this->getEntry((int)$churchId);
    }

    private function createSlug($name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $name = str_replace([' ', '/', '(', ')'], '-', $name);
        $name = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $name);
        $name = preg_replace("/[^a-z-0-9]+/i", "", $name);
        while (strpos($name, '--') !== false) {
            $name = str_replace('--', '-', $name);
        }
        $name = trim($name, '-');

        $name = substr($name, 0, 95);

        $slug = $name;
        $iteration = 1;
        while ($this->getIDForSlug($slug)) {
            $iteration++;
            $slug = $name . '-' . $iteration;
        }

        return $slug;
    }

    public function getIDForSlug(string $slug)
    {
        $statement = $this->connection->prepare('
			SELECT id FROM churches
            WHERE slug = :slug');
        $statement->bindParam(':slug', $slug);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : false;
    }

    public function getSettings(): array
    {
        $statement = $this->connection->query('SELECT name, value
			FROM settings');
        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
