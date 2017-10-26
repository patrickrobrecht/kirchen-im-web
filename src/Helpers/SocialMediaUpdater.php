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
        $id = intval($row['cid']);
        $url = $row['url'];
        $followersNew = $this->getFollowers($row['type'], $url);
        $data = [
            'id' => $id,
            'url' => $url,
            'type' => $row['type'],
            'followers' => $followersNew,
            'followersOld' => is_null($row['followers']) ? null : intval($row['followers'])
        ];

        if ($followersNew) {
            // Update follower number and the timestamp.
            $data['followers_updated'] = Database::getInstance()->updateFollowers($url, $followersNew);
        } else {
            // Update the timestamp
            $data['timestamp_updated'] = Database::getInstance()->updateFollowers($url, false);
        }

        return $data;
    }

    /**
     * Returns the latest follower count for the given URL.
     *
     * @param string $network the social network
     * @param string $url the URL to check
     *
     * @return int the follower count; negative value in case of errors.
     */
    private function getFollowers($network, $url) {
        switch ($network) {
            case 'facebook':
                return $this->getFacebookLikes($url);
            case 'googlePlus':
                return $this->getGooglePlusOnes($url);
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
     * @return int the number of likes, or false on failure
     */
    private function getFacebookLikes($url) {
        try {
            $id = substr($url, 25); // 25 = strlen('https://www.facebook.com/')
            if (SocialMediaUpdater::startsWith($id, 'groups/')) {
                return 0;
            }
            if ( SocialMediaUpdater::startsWith($id, 'pages/') ) {
                $temp = explode('/', $id);
                $id = end($temp);
            }

            //Construct a Facebook URL
            $json_url ='https://graph.facebook.com/' . $id .
                       '?access_token='.FACEBOOK_API_ID.'|'.FACEBOOK_API_SECRET.'&fields=likes';
            $json = @file_get_contents($json_url);
            if (!$json) {
                $temp = explode('-', $id);
                $id = str_replace('/', '', end($temp) );
                $json_url ='https://graph.facebook.com/' . $id .
                           '?access_token='.FACEBOOK_API_ID.'|'.FACEBOOK_API_SECRET.'&fields=likes';
                $json = @file_get_contents($json_url);
                if (!$json) {
                    return false;
                }
            }
            $json_output = json_decode($json);

            //Extract the likes count from the JSON object
            if ($json_output->likes) {
                return $json_output->likes;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of Google+ subscribers.
     *
     * @param string $url the Google+ URL to check
     *
     * @return int the number of +1s, or false on failure
     */
    private function getGooglePlusOnes($url) {
        try {
            $id = substr($url, 24);
            $json_url = 'https://www.googleapis.com/plus/v1/people/' . $id .'?key=' . GOOGLE_API_KEY;
            $json = @file_get_contents($json_url);
            if (!$json) {
                return false;
            }
            $json_output = json_decode($json);

            return intval($json_output->circledByCount);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of Twitter followers of the given URL.
     *
     * @param string $url the Twitter URL to check
     *
     * @return int the number of subscribers, or false on failure
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
            $follow_count = $twitterAPI->setGetfield('?screen_name=' . $name)
                                       ->buildOauth('https://api.twitter.com/1.1/users/show.json', 'GET')
                                       ->performRequest();
            $data = json_decode($follow_count, true);
            if (!$data || !isset($data['followers_count']) ) {
                return false;
            }
            return $data['followers_count'];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the number of YouTube subscribers of the given URL.
     *
     * @param string $url the YouTube URL to check
     *
     * @return int the number of subscribers, or false on failure
     */
    private function getYoutubeSubscribers($url) {
        try {
            $username = substr($url, 24);
            if ( SocialMediaUpdater::startsWith($username, 'channel/') ) {
                $temp = explode('/', $username);
                $channelId = end($temp);
            } else {
                $json_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $username
                            . '&type=channel&key=' . GOOGLE_API_KEY;
                $json = @file_get_contents($json_url);
                if (!$json) {
                    return false;
                }
                $json_output = json_decode($json);
                $channelId = $json_output->items[0]->id->channelId;
            }

            $json_url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channelId
                        . '&key=' . GOOGLE_API_KEY;
            $json = @file_get_contents($json_url);
            if (!$json) {
                return false;
            }
            $json_output = json_decode($json);

            return intval($json_output->items[0]->statistics->subscriberCount);
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