<?php

namespace KirchenImWeb\Updaters;

use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use InstagramScraper\Instagram;
use KirchenImWeb\Helpers\Database;
use Phpfastcache\Helper\Psr16Adapter;
use TwitterAPIExchange;

/**
 * Class SocialMediaUpdater
 *
 * @package KirchenImWeb\Updaters
 */
class SocialMediaUpdater
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var Instagram
     */
    private $instagram;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function updateNetwork(string $network, int $count): void
    {
        echo sprintf('SOCIAL MEDIA UPDATER: %d entries for %s.', $count, $network) . PHP_EOL;

        $urls = $this->database->getSocialMediaForUpdate($network, $count);
        foreach ($urls as $row) {
            $data = $this->update($row);
            echo sprintf(
                '%s - old: %d - new: %d',
                $data['url'],
                $data['followersOld'],
                $data['followersNew']
            ) . PHP_EOL;
        }
    }

    private function update(array $row): array
    {
        $websiteId = (int)$row['websiteId'];
        $url = $row['url'];
        $followersNew = $this->getFollowers($row['type'], $url);
        $data = [
            'churchId' => (int)$row['churchId'],
            'url' => $url,
            'followersNew' => $followersNew,
            'followersOld' => $row['followers'] === null ? null : (int)$row['followers']
        ];

        if ($followersNew && $followersNew > 0) {
            // Update follower number and the timestamp.
            $this->database->updateFollowers($websiteId, $followersNew);
            $this->database->addFollowers($websiteId, $followersNew);
        } else {
            // Update the timestamp.
            $this->database->updateFollowers($websiteId, false);
        }

        return $data;
    }

    /**
     * Returns the latest follower count for the given URL.
     *
     * @param string $network the social network
     * @param string $url the URL to check
     *
     * @return int|bool the follower count; false in case of errors.
     */
    private function getFollowers(string $network, string $url)
    {
        switch ($network) {
            case 'facebook':
                return self::getFacebookLikes($url);
            case 'instagram':
                return $this->getInstagramFollowers($url);
            case 'twitter':
                return self::getTwitterFollower($url);
            default:
                return false;
        }
    }

    /**
     * Returns the number of likes of the given Facebook page.
     *
     * @param string $url the URL of the Facebook page to check
     *
     * @return int|bool the number of likes, or false on failure
     */
    private static function getFacebookLikes(string $url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, LinkChecker::USER_AGENT);
        $htmlCode = curl_exec($handle);

        if ($htmlCode && curl_getinfo($handle, CURLINFO_RESPONSE_CODE) === 200) {
            $followerCount = self::extractFollowerCountFromMeta($htmlCode, 'property', 'og:description');
            if ($followerCount) {
                return $followerCount;
            }

            return self::extractFollowerCountFromMeta($htmlCode, 'name', 'description');
        }
        return false;
    }

    /**
     * Extracts the follower count from the meta element with the given name.
     *
     * @param string $metaKey the attribute to compare
     * @param string $metaValue the attribute to expect
     *
     * @return bool|int
     */
    private static function extractFollowerCountFromMeta(string $html, string $metaKey, string $metaValue)
    {
        $metaContent = self::getMetaContent($html, $metaKey, $metaValue);
        if ($metaContent) {
            preg_match('/Gefällt (?P<likes>\d+(.\d+)*) Mal/mu', $metaContent, $match);
            if (isset($match['likes'])) {
                $i = (int)str_replace('.', '', $match['likes']);
                if ($i > 0) {
                    return $i;
                }
            }
        }

        return false;
    }

    /**
     * Extracts the first meta content element with the given attribute from the given HTML code.
     *
     * @param string $html the HTML
     * @param string $metaKey the attribute to compare
     * @param string $metaValue the attribute to expect
     *
     * @return bool|string the meta description, or false on failure
     */
    private static function getMetaContent(string $html, string $metaKey, string $metaValue)
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        foreach ($doc->getElementsByTagName('meta') as $node) {
            /** @var $node \DOMNode */
            if ($metaValue === strtolower($node->getAttribute($metaKey))) {
                return $node->getAttribute('content');
            }
        }
        return false;
    }

    /**
     * Returns the number of Instagram followers.
     *
     * @param string $url the Instagram to check
     *
     * @return int|bool the number of followers, or false on failure
     */
    private function getInstagramFollowers(string $url)
    {
        try {
            $name = str_replace('/', '', substr($url, 25));
            $instagram = $this->getInstagram();
            if (!$instagram) {
                return false;
            }
            $account = $instagram->getAccount($name);
            return $account->getFollowedByCount();
        } catch (Exception $e) {
            return false;
        }
    }

    private function getInstagram(): ?Instagram
    {
        if (!$this->instagram) {
            $this->instagram = Instagram::withCredentials(
                new Client(),
                INSTAGRAM_USERNAME,
                INSTAGRAM_PASSWORD,
                new Psr16Adapter('Files')
            );
            $this->instagram->setUserAgent(LinkChecker::USER_AGENT);
            try {
                $this->instagram->login();
                $this->instagram->saveSession();
            } catch (Exception $e) {
            }
        }
        return $this->instagram;
    }

    /**
     * Returns the number of Twitter followers of the given URL.
     *
     * @param string $url the Twitter URL to check
     *
     * @return int|bool the number of subscribers, or false on failure
     */
    private static function getTwitterFollower(string $url)
    {
        try {
            $name = substr($url, 20);
            $settings = [
                'oauth_access_token' => TWITTER_API_TOKEN,
                'oauth_access_token_secret' => TWITTER_API_TOKEN_SECRET,
                'consumer_key' => TWITTER_API_CONSUMER_KEY,
                'consumer_secret' => TWITTER_API_CONSUMER_SECRET
            ];
            $twitterAPI = new TwitterAPIExchange($settings);
            $json = $twitterAPI->setGetfield('?screen_name=' . $name)
                               ->buildOauth('https://api.twitter.com/1.1/users/show.json', 'GET')
                               ->performRequest();
            $json = json_decode($json, false);
            if (isset($json->followers_count)) {
                return (int)$json->followers_count;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function run(Database $database, int $count): void
    {
        new SocialMediaUpdater($database, $count);
    }
}
