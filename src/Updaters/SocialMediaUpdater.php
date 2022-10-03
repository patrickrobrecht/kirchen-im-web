<?php

namespace KirchenImWeb\Updaters;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use InstagramScraper\Instagram;
use JsonException;
use KirchenImWeb\Helpers\Database;
use Phpfastcache\Helper\Psr16Adapter;
use Psr\SimpleCache\InvalidArgumentException;
use TwitterAPIExchange;

/**
 * Class SocialMediaUpdater
 *
 * @package KirchenImWeb\Updaters
 */
class SocialMediaUpdater
{
    private Database $database;
    private ?Client $facebookClient = null;
    private ?Instagram $instagram = null;
    private ?TwitterAPIExchange $twitter = null;

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
     * @return int|bool the follower count; false in case of errors.
     */
    private function getFollowers(string $network, string $url): bool | int
    {
        return match ($network) {
            'facebook' => $this->getFacebookLikes($url),
            'instagram' => $this->getInstagramFollowers($url),
            'twitter' => $this->getTwitterFollower($url),
            default => false,
        };
    }

    /**
     * Returns the number of likes of the given Facebook page.
     *
     * @param string $url the URL of the Facebook page to check
     *
     * @return int|bool the number of likes, or false on failure
     */
    private function getFacebookLikes(string $url): bool | int
    {
        try {
            if (!$this->facebookClient) {
                $this->facebookClient = new Client([
	                'base_uri' => 'https://graph.facebook.com/v2.10/',
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

    /**
     * Returns the number of Instagram followers.
     *
     * @param string $url the Instagram to check
     *
     * @return int|bool the number of followers, or false on failure
     */
    private function getInstagramFollowers(string $url): bool | int
    {
        try {
            $name = str_replace('/', '', substr($url, 25));
            $instagram = $this->getInstagram();
            if (!$instagram) {
                return false;
            }
            $account = $instagram->getAccount($name);
            return $account->getFollowedByCount();
        } catch (Exception) {
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
            } catch (Exception | InvalidArgumentException) {
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
    private function getTwitterFollower(string $url): bool | int
    {
        try {
            $name = substr($url, 20);
            $twitterAPI = $this->getTwitter();
            if (!$twitterAPI) {
                return false;
            }
            $json = $twitterAPI->setGetfield('?screen_name=' . $name)
                               ->buildOauth('https://api.twitter.com/1.1/users/show.json', 'GET')
                               ->performRequest();
            $json = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
            if (isset($json->followers_count)) {
                return (int)$json->followers_count;
            }
            return false;
        } catch (Exception) {
            return false;
        }
    }

    private function getTwitter(): ?TwitterAPIExchange
    {
        if (!$this->twitter) {
            $this->twitter = new TwitterAPIExchange(
                [
                    'oauth_access_token' => TWITTER_API_TOKEN,
                    'oauth_access_token_secret' => TWITTER_API_TOKEN_SECRET,
                    'consumer_key' => TWITTER_API_CONSUMER_KEY,
                    'consumer_secret' => TWITTER_API_CONSUMER_SECRET
                ]
            );
        }
        return $this->twitter;
    }
}
