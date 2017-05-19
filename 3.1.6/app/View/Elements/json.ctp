<?php
	/**
	* INFO:
	* 	- http://bakery.cakephp.org/articles/jrbasso/2008/10/26/generating-automatized-json-as-output
	* 	- http://blog.pagebakers.nl/2007/06/05/using-json-in-cakephp-12/
	*/

	$content = json_encode( $json );

	header( 'Pragma: no-cache' );
	header( 'Cache-Control: no-store, no-cache, max-age=0, must-revalidate' );
	header( 'Content-Type: text/x-json' );
	header( "X-JSON: {$content}" );

	Configure::write( 'debug', 0 );
	echo $content;
?>