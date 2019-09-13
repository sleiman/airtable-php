<?php

require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Airtable.php';
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Request.php';
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Response.php';

$a = new \TANIOS\Airtable\Airtable( [
    'base'          => '###',
    'api_key'       => '###'
] );

/**
 * Batch creating
 */

$content = $a->saveContent( 'Links', [
    [
        'fields'            => [
            'Name'          => 'Tasty'
        ]
    ],
    [
        'fields'            => [
            'Name'          => 'Yolo'
        ]
    ]
] );

/**
 * Batch updating
 */

$update = [];

foreach ( $content->records as $record )
{
    $update[] = [
        'id'            => $record->id,
        'fields'        => [
            'Slug'      => strtolower( $record->fields->Name )
        ]
    ];
}

$response = $a->updateContent( 'Links', $update );

/**
 * Batch deleting
 */

$delete = [];

foreach ( $response->records as $record )
{
    $delete[] = $record->id;
}

$response = $a->deleteContent( 'Links', $delete );