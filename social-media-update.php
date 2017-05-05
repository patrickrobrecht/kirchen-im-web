<?php
ini_set('max_execution_time', 300);

require_once dirname(__FILE__) . '/includes/config.php';
require_once dirname(__FILE__) . '/includes/TwitterAPIExchange.php';

// Create list of networks to compare (escaped, comma-separated)
global $networksToCompare;
$networksToCompareAsStrings = array();
foreach ($networksToCompare as $type => $typeName) {
	array_push($networksToCompareAsStrings, "'" . $type . "'");
}
$networksToCompareList = implode(', ', $networksToCompareAsStrings);

// Start update and measure time.
echo '<p>Started updating like data for ' . $networksToCompareList .  '.</p>';
$time_start = microtime(true);
updateEntriesForType($networksToCompareList, 10);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
echo '<p>Completed in ' . $execution_time . ' seconds.</p>';

/**
 * Updates the number of followers for the entries for the given networks
 *
 * @param string $networksToCompareList the comma-separated list of networks to consider
 * @param number $limit the maximum number of entries to check
 */
function updateEntriesForType($networksToCompareList, $limit) {
	global $connection;
	$query = 'SELECT cid, url, type, followers, timestamp 
		FROM websites
		WHERE type IN (' . $networksToCompareList . ') 
		ORDER BY timestamp 
		LIMIT ' . $limit;
	$statement = $connection->query($query);
	echo '<ol>';
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$id = $row['cid'];
		$url = $row['url'];
		$followers = is_null($row['followers']) ? -1 : $row['followers'];
		$followersNew = getFollowers($row['type'], $url);
		if ($followersNew >= 0 && $followers != $followersNew) {
			// Update follower number and the timestamp.
			$insertStatement = $connection->prepare('
				UPDATE websites 
				SET followers = :followers, timestamp = NOW()
				WHERE url = :url
			');
			$insertStatement->bindParam(':url', $url);
			$insertStatement->bindParam(':followers', $followersNew, PDO::PARAM_INT);
			$insertStatement->execute();
			echo '<li><a href="details.php?id=' . $id . '">Updated ' . $id . '</a> '
					. '<a href="' . $url . '">' . $url . "</a>"
					. ' (' . $followers . ' to ' . $followersNew . ')</li>';
		} else {
			// Update the timestamp
			$insertStatement = $connection->prepare('
				UPDATE websites
				SET timestamp = NOW()
				WHERE url = :url
			');
			$insertStatement->bindParam(':url', $url);
			$insertStatement->execute();
			echo '<li><a href="details.php?id=' . $id . '">Checked ' . $id . '</a> '
					. '<a href="' . $url . '">' . $url . "</a>"
					. ' (' . $followersNew . ')</li>';
		}
	}
	echo '</ol>';
}

/**
 * Returns the latest follower count for the given URL.
 *
 * @param string $network the social network
 * @param string $url the URL to check
 *
 * @return int the follower count; negative value in case of errors.
 */
function getFollowers($network, $url) {
	switch ($network) {
		case 'facebook':
			return getFacebookLikes($url);
		case 'googlePlus':
			return getGooglePlusOnes($url);
		case 'twitter':
			return getTwitterFollower($url);
		case 'youtube':
			return getYoutubeSubscribers($url);
		default:
			// error
			return -2;
	}
}

/**
 * Returns the number of likes of the given Facebook page.
 *
 * @param string $url the URL of the Facebook page to check
 *
 * @return int the number of likes, or -1 on failure
 */
function getFacebookLikes($url) {
	try {
        $id = substr($url, 25); // 25 = strlen('https://www.facebook.com/')
        if ( startsWith($id, 'groups/') ) {
            return 0;
        }
        if ( startsWith($id, 'pages/') ) {
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
				return -1;
			}
		}
		$json_output = json_decode($json);

		//Extract the likes count from the JSON object
		if ($json_output->likes) {
			return $json_output->likes;
		} else {
			return -1;
		}
	} catch (Exception $e) {
		return -1;
	}
}

/**
 * Returns the number of Google+ subscribers.
 *
 * @param string $url the Google+ URL to check
 *
 * @return int the number of +1s, or -1 on failure
 */
function getGooglePlusOnes($url) {
	try {
		$id = substr($url, 24);
		$json_url = 'https://www.googleapis.com/plus/v1/people/' . $id .'?key=' . GOOGLE_API_KEY;
		$json = @file_get_contents($json_url);
		if (!$json) {
			return -1;
		}
		$json_output = json_decode($json);

		return intval($json_output->circledByCount);
	} catch (Exception $e) {
		return -1;
	}
}

/**
 * Returns the number of Twitter followers of the given URL.
 *
 * @param string $url the Twitter URL to check
 *
 * @return int the number of subscribers, or -1 on failure
 */
function getTwitterFollower($url) {
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
			return -1;
		}
		return $data['followers_count'];
	} catch (Exception $e) {
		return -1;
	}
}

/**
 * Returns the number of YouTube subscribers of the given URL.
 *
 * @param string $url the YouTube URL to check
 *
 * @return int the number of subscribers, or -1 on failure
 */
function getYoutubeSubscribers($url) {
	try {
		$username = substr($url, 24);
		if ( startsWith($username, 'channel/') ) {
			$temp = explode('/', $username);
			$channelId = end($temp);
		} else {
			$json_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $username
				. '&type=channel&key=' . GOOGLE_API_KEY;
			$json = @file_get_contents($json_url);
			if (!$json) {
				return -1;
			}
			$json_output = json_decode($json);
			$channelId = $json_output->items[0]->id->channelId;
		}

		$json_url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channelId
			. '&key=' . GOOGLE_API_KEY;
		$json = @file_get_contents($json_url);
		if (!$json) {
			return -1;
		}
		$json_output = json_decode($json);

		return intval($json_output->items[0]->statistics->subscriberCount);
	} catch (Exception $e) {
		return -1;
	}
}

/**
 * Tests whether the given haystack starts with needle.
 *
 * @param string $haystack the haystack
 * @param string $needle the needle
 * @return boolean true if and only if the haystack starts with needle
 */
function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
