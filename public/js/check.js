/**
 * Check the validity of an input URL for social media sites.
 */
function checkURL(input) {
	var urlStart;
	
	switch (input.id) {
	case 'facebookURL':
		urlStart = 'https://www.facebook.com/';
		break;
	case 'flickrURL': 
		urlStart = 'https://www.flickr.com/';
		break;
	case 'googlePlusURL':
		urlStart = 'https://plus.google.com/';
		break;
	case 'instagramURL':
		urlStart = 'https://www.instagram.com/';
		break;
	case 'twitterURL':
		urlStart = 'https://twitter.com/';
		break;
	case 'youtubeURL':
		urlStart = 'https://www.youtube.com/';
		break;
	default:
		urlStart = '';
		break;
	}

	if (input.value.startsWith(urlStart)) {
		// input is valid: reset the error message
		input.setCustomValidity('');
	} else {
		input.setCustomValidity('URL muss mit ' + urlStart + ' beginnen!');
	}
}
