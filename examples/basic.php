<?php

// Simple example to setup and retrieve all data from a table 

// If using Composer
require 'vendor/autoload.php';

/* if not using composer, uncomment this
include('../src/Airtable.php');
include('../src/Request.php');
include('../src/Response.php');
*/

use TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key'   => 'API_KEY',
    'base'      => 'BASE_ID',
));

$request = $airtable->getContent( 'Table Name' );

do {

    $response = $request->getResponse();

    var_dump( $response[ 'records' ] );

}
while( $request = $response->next() );
