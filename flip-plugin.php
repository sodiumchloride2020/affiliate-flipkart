<?php
/*
Plugin Name: Flipkart Affiliate
Plugin URI: https://github.com/sodiumchloride2020/affiliate-flipkart
Description: Add your FLipkart-Tag to all Amazon URLs before redirection
Version: 1.2
Author: Amogh Kharche
Author URI: https://realjobs.in
*/

yourls_add_action('pre_redirect', 'flo_amazonAffiliate');

function flo_amazonAffiliate($args) {
	// insert your personal settings here
	$tagIN = 'vivekdn250';
	$campaign = 'realjobs.in';

	// get url from arguments; create dictionary with all regex patterns and their respective affiliate tag as key/value pairs
	$url = $args[0];
	$patternTagPairs = array(
		'/^http(s)?:\\/\\/(www\\.)?flipkart.com+/ui' => $tagIN,
	);

	// check if URL is a supported Flipkart URL
	foreach ($patternTagPairs as $pattern => $tag) {
		if (preg_match($pattern, $url) == true) {
			// matched URL, now modify URL
			$url = cleanUpURL($url);
			$url = addTagToURL($url, $tag);
			$url = addCampaignToURL($url, $campaign);

			// redirect
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url");

			// now die so the normal flow of event is interrupted
			die();
		}
	}
}

function cleanUpURL($url) {
	// check if last char is an "/" (in case it is, remove it)
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}

	// remove existing affiliate tag if needed
	$existingTag;
	if (preg_match('/affid=.+&?/ui', $url, $matches) == true) {
		$existingTag = $matches[0];
	}
	if ($existingTag) {
		$url = str_replace($existingTag, "", $url);
	}

	// remove existing campaign if needed
	$existingCampagin;
	if (preg_match('/camp=.+&?/ui', $url, $matches) == true) {
		$existingCampagin = $matches[0];
	}
	if ($existingCampagin) {
		$url = str_replace($existingCampagin, "", $url);
	}

	return $url;
}

function addTagToURL($url, $tag) {
	// add our tag to the URL
	if (strpos($url, '?') !== false) {
		// there's already a query string in our URL, so add our tag with "&"
		// add tag depending on if we need to add a "&" or not
		if (substr($url, -1) == "&") {
			$url = $url.'affid='.$tag;
		} else {
			$url = $url.'&affid='.$tag;
		}
	} else { // start a new query string
		$url = $url.'?affid='.$tag;
	}

	return $url;
}

function addCampaignToURL($url, $campaign) {
	if (empty($campaign)) {
		return $url;
	}
	$url = $url.'&camp='.$campaign;

	return $url;
}

?>
