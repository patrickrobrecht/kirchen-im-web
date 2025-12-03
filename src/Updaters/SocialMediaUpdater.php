<?php

namespace KirchenImWeb\Updaters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use KirchenImWeb\Helpers\Database;

class SocialMediaUpdater
{
    private Database $database;
    private ?Client $facebookClient = null;

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
        $websiteId = (int) $row['websiteId'];
        $url = $row['url'];
        $followersNew = $this->getFollowers($row['type'], $url);
        $data = [
            'churchId' => (int) $row['churchId'],
            'url' => $url,
            'followersNew' => $followersNew,
            'followersOld' => $row['followers'] === null ? null : (int) $row['followers'],
        ];

        if ($followersNew !== false) {
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
     * @return bool|int the follower count; false in case of errors.
     */
    private function getFollowers(string $network, string $url): bool|int
    {
        return match ($network) {
            'facebook' => $this->getFacebookLikes($url),
            default => false,
        };
    }

    /**
     * Returns the number of likes of the given Facebook page.
     *
     * @param string $url the URL of the Facebook page to check
     *
     * @return bool|int the number of likes, or false on failure
     */
    private function getFacebookLikes(string $url): bool|int
    {
        try {
            if (!$this->facebookClient) {
                $this->facebookClient = new Client([
                    'base_uri' => 'https://graph.facebook.com/',
                ]);
            }

            $pageName = substr($url, 25);

            $accessToken = FACEBOOK_APP_ID . '|' . FACEBOOK_APP_SECRET;
            $response = $this->facebookClient->get($pageName, [
                RequestOptions::QUERY => [
                    'access_token' => $accessToken,
                    'appsecret_proof' => hash_hmac('sha256', $accessToken, FACEBOOK_APP_SECRET),
                    'fields' => 'id,fan_count',
                ],
            ]);
            $json = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

            return $json->fan_count ?? false;
        } catch (GuzzleException|JsonException) {
            return false;
        }
    }
}
