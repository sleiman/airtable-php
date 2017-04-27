<?php

error_reporting( E_ALL );

include('../src/Airtable.php');
include('../src/Request.php');
include('../src/Response.php');

use TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key'   => 'API_KEY',
    'base'      => 'BASE_ID',
));

$request = $airtable->getContent( 'Partenaires%20de%20salles' );

do {

    $response = $request->getResponse();

    var_dump( $response[ 'records' ] );

}
while( $request = $response->next() );


