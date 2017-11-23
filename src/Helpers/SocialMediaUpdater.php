<?php
namespace KirchenImWeb\Helpers;

use Exception;
use TwitterAPIExchange;

/**
 * Class SocialMediaUpdater
 *
 * @package KirchenImWeb\Helpers
 */
class SocialMediaUpdater extends AbstractHelper {

    public function cron() {
        ini_set('max_execution_time', 300);

        // Create list of networks to compare (escaped, comma-separated)
        $networksToCompare = Configuration::getInstance()->networksToCompare;
        $networksToCompareAsStrings = array();
        foreach ($networksToCompare as $type => $typeName) {
            array_push($networksToCompareAsStrings, "'" . $type . "'");
        }
        $networksToCompareList = implode(', ', $networksToCompareAsStrings);

        // Start update and measure time.
        $time = microtime(true);
        $urls = Database::getInstance()->getURLsForUpdate($networksToCompareList, 10);
        $results = [];
        foreach ($urls as $row) {
            array_push($results, $this->update($row));
        }
        $duration = (microtime(true) - $time);

        return [
            'updatedEntries' => $results,
            'included' => $networksToCompare,
            'duration' => $duration
        ];
    }

    private function update($row) {
	    $websiteId = intval($row['websiteId']);
        $url = $row['url'];
        $followersNew = $this->getFollowers($row['type'], $url);
        $data = [
            'churchId' => intval($row['churchId']),
            'url' => $url,
            'type' => $row['type'],
            'followers' => $followersNew,
            'followersOld' => is_null($row['followers']) ? null : intval($row['followers'])
        ];

        if ($followersNew >= 0) {
            // Update follower number and the timestamp.
            if (Database::getInstance()->updateFollowers($websiteId, $followersNew)) {
                $data['updated'] = 'followers';
	            Database::getInstance()->addFollowers($websiteId, $followersNew);
            }
        } else {
            // Update the timestamp
            if (Database::getInstance()->updateFollowers($url, false)) {
                $data['updated'] = 'timestamp';
            }
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
    private function getFollowers($network, $url) {
        switch ($network) {
            case 'facebook':
                return $this->getFacebookLikes($url);
            case 'googlePlus':
                return $this->getGooglePlusOnes($url);
            case 'instagram':
                return $this->getInstagramFollowers($url);
            case 'twitter':
                return $this->getTwitterFollower($url);
            case 'youtube':
                return $this->getYoutubeSubscribers($url);
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
    private function getFacebookLikes($url) {
        try {
            $id = substr($url, 25); // 25 = strlen('https://www.facebook.com/')
            if (SocialMediaUpdater::startsWith($id, 'groups/')) {
                return false;
            }
            if (SocialMediaUpdater::startsWith($id, 'pages/')) {
                $temp = explode('/', $id);
                $id = end($temp);
            }

            $json_url ='https://graph.facebook.com/' . $id .
                       '?access_token='.FACEBOOK_API_ID.'|'.FACEBOOK_API_SECRET.'&fields=likes';
            $json = @file_get_contents($json_url);
            if (!$json) {
                $temp = explode('-', $id);
                $id = str_replace('/', '', end($temp) );
                $json_url ='https://graph.facebook.com/' . $id .
                           '?access_token='.FACEBOOK_API_ID.'|'.FACEBOOK_API_SECRET.'&fields=likes';
                $json = @file_get_contents($json_url);
            }

            if ($json) {
                $json = json_decode($json);
                if (isset($json->likes)) {
                    return $json->likes;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of Google+ subscribers.
     *
     * @param string $url the Google+ URL to check
     *
     * @return int|bool the number of +1s, or false on failure
     */
    private function getGooglePlusOnes($url) {
        try {
            $id = substr($url, 24);
            $json_url = 'https://www.googleapis.com/plus/v1/people/' . $id .'?key=' . GOOGLE_API_KEY;
            $json = @file_get_contents($json_url);
            if ($json) {
                $json = json_decode($json);
                if (isset($json->circledByCount)) {
                    return intval($json->circledByCount);
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of Instagram followers.
     *
     * @param string $url the Instagram to check
     *
     * @return int|bool the number of +1s, or false on failure
     */
    private function getInstagramFollowers($url) {
        try {
            $json = @file_get_contents($url . '?__a=1');
            if ($json) {
                $json = json_decode($json);
                if (isset($json->user->followed_by->count)) {
                    return intval($json->user->followed_by->count);
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of Twitter followers of the given URL.
     *
     * @param string $url the Twitter URL to check
     *
     * @return int|bool the number of subscribers, or false on failure
     */
    private function getTwitterFollower($url) {
        try {
            $name = substr($url, 20);
            $settings = array(
                'oauth_access_token' => TWITTER_API_TOKEN,
                'oauth_access_token_secret' => TWITTER_API_TOKEN_SECRET,
                'consumer_key' => TWITTER_API_CONSUMER_KEY,
                'consumer_secret' => TWITTER_API_CONSUMER_SECRET
            );
            $twitterAPI = new TwitterAPIExchange($settings);
            $json = $twitterAPI->setGetfield('?screen_name=' . $name)
                               ->buildOauth('https://api.twitter.com/1.1/users/show.json', 'GET')
                               ->performRequest();
            $json = json_decode($json);
            if (isset($json->followers_count) ) {
                return intval($json->followers_count);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of YouTube subscribers of the given URL.
     *
     * @param string $url the YouTube URL to check
     *
     * @return int|bool the number of subscribers, or false on failure
     */
    private function getYoutubeSubscribers($url) {
        try {
            $username = substr($url, 24);
            $channelId = false;
            if ( SocialMediaUpdater::startsWith($username, 'channel/') ) {
                $temp = explode('/', $username);
                $channelId = end($temp);
            } else {
                $json_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $username
                            . '&type=channel&key=' . GOOGLE_API_KEY;
                $json = @file_get_contents($json_url);
                if ($json) {
                    $json = json_decode($json);
                    if (isset($json->items[0]->id->channelId)) {
                        $channelId = $json->items[0]->id->channelId;
                    }
                }
            }

            if ($channelId) {
                $json_url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channelId
                            . '&key=' . GOOGLE_API_KEY;
                $json = @file_get_contents($json_url);
                if ($json) {
                    $json = json_decode($json);
                    if (isset($json->items[0]->statistics->subscriberCount)) {
                        return intval($json->items[0]->statistics->subscriberCount);
                    }
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Tests whether the given haystack starts with needle.
     *
     * @param string $haystack the haystack
     * @param string $needle the needle
     * @return boolean true if and only if the haystack starts with needle
     */
    private static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
}