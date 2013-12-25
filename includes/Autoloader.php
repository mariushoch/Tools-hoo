<?php

function hooAutoloader( $class ) {
	$classes = array(
		'hoo\DatabaseConnect' => 'DatabaseConnect.php',
		'hoo\Replag' => 'Replag.php',
		'hoo\Request' => 'Request.php',
		'hoo\InputValidation' => 'InputValidation.php',
		'hoo\DatabaseNameLookup' => 'DatabaseNameLookup.php',
		'hoo\Api\Exception' => 'api/Exception.php',
		'hoo\Api\RequestHandler' => 'api/RequestHandler.php',
		'hoo\Api\Base' => 'api/Base.php',
		'hoo\Api\ActiveSysops' => 'api/ActiveSysops.php',

		// Test stuff
		'hoo\Test\TestHelper' => '../tests/phpunit/TestHelper.php',
	);

	if( isset( $classes[ $class ] ) ) {
		require_once( __DIR__ . '/' . $classes[ $class ] );
	}
}

spl_autoload_register( 'hooAutoloader' );
