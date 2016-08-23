<?php
	$self = basename( $_SERVER['PHP_SELF'] );
?>

	<footer>
		<nav>
			<ul><?php foreach ($footerLinks as $text => $url) { 
					echoLinkOrText($text, $url, $url == $self);
				} ?></ul>
		</nav>
	</footer>
