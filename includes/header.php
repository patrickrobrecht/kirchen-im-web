<?php
    global $current_language, $headerLinks, $languages_slugs, $languages_names;
	$self = basename( $_SERVER['PHP_SELF'] );
	parse_str($_SERVER['QUERY_STRING'], $parameters);
	unset($parameters['lang']);
	$parameterString = http_build_query( $parameters );
	if ($parameterString != '') {
		$self .= '?' . $parameterString;
	}
?>

	<header>
		<nav>
			<ul><?php 
					// echo header links
					foreach ($headerLinks as $text => $url) { 
						echoLinkOrText($text, $url, $url == $self);
					}
					// echo language links					
					foreach ($languages_slugs as $language => $slug) {
						if ($language != $current_language) {
							echoLanguageLink($languages_names[$language], $slug, $self);
						}
					}?></ul>
		</nav>
	</header>
