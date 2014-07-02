<?php

use hoo\ModuleSelector;
use hoo\Request;
use hoo\Database\DatabaseConnect;
use hoo\Database\DatabaseNameLookup;

require_once __DIR__ . '/includes/WebStart.php';

$mainRequest = new Request( $_GET, $_POST );

if ( $mainRequest->getInput( 'action' ) !== 'activeSysops' ) {
	die( 'Not supported, yet' );
}

$moduleSelector = new ModuleSelector();
$apiModule = $moduleSelector->getHandler();

$apiHandle = new $apiModule(
		$mainRequest,
		new DatabaseConnect(),
		new DatabaseNameLookup()
);

$json = json_encode( $apiHandle->execute() );

if ( $mainRequest->getInput( 'callback' ) ) {
	echo $mainRequest->getInput( 'callback' ) . '(' . $json . ');';
} else {
	echo $json;
}