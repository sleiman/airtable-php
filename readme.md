> We are working on new features, help us decide what's next. 
> [Give your opinion](https://ocb.one/2l2)

# Airtable PHP client
A PHP client for the Airtable API. 
Comments, requests or bug reports are appreciated.

[View examples](examples)

## Get started

Please note that Airtable doesn't allow schema manipulation using their public API, you have to create your tables using their interface.

Once you created your base in the Airtable Interface open the API Docs to get your Base ID.


<img src="examples/img/api-doc-b.png" alt="API Doc Airtable"  width="200">

The Base ID is a code that starts with 'app' followed by a mix of letter or numbers (appsvqGDFCwLC3I10).

---

### Installation

If you're using Composer, you can run the following command:
```
composer require sleiman/airtable-php
```
You can also download them directly and extract them to your web directory.


### Add the wrapper to your project
If you're using Composer, run the autoloader
```php
require 'vendor/autoload.php';
```
Or include the Airtable.php file

```php
include('../src/Airtable.php');
include('../src/Request.php');
include('../src/Response.php');
```
### Initialize the class
```php
use \TANIOS\Airtable\Airtable;
$airtable = new Airtable(array(
    'api_key' => 'API_KEY',
    'base'    => 'BASE_ID'
));
```
### Get all entries in table
We are getting all the entries from the table "Contacts". 
```php
$request = $airtable->getContent( 'Contacts' );

do {
    $response = $request->getResponse();
    var_dump( $response[ 'records' ] );
}
while( $request = $response->next() );

print_r($request);
```
### Use params to filter, sort, etc
```php
// You don't have to use all the params, they are added as a reference
$params = array(
    "filterByFormula" => "AND( Status = 'New' )",
    "sort" => array(array('field' => 'Count', 'direction' => "desc")),
    "maxRecords" => 175,
    "pageSize" => 50,
    "view" => "Name of your View"
);

$request = $airtable->getContent( 'Contacts', $params);

do {
    $response = $request->getResponse();
    var_dump( $response[ 'records' ] );
}
while( $request = $response->next() );

print_r($request);
```
### Create new entry
We will create new entry in the table Contacts
```php
// Create an array with all the fields you want 
$new_contact_details = array(
    'Name'        =>"Contact Name",
    'Address'     => "1234 Street Name, City, State, Zip, Country",
    'Telephone #' => '123-532-1239',
    'Email'       =>'email@domain.com',
);

// Save to Airtable
$new_contact = $airtable->saveContent( "Contacts", $new_contact_details );

// The ID of the new entry
echo $new_contact->id;

print_r($new_contact);
```
*Batch Create now available, documentation available below*

### Update Contact
Use the entry ID to update the entry
```php
$update_contact_details = array(
	'Telephone #' => '514-123-2942',
);
$update_contact = $airtable->updateContent("Contacts/{entry-id}",$update_contact_details);
print_r($update_contact);
```
*Batch Update now available, documentation available below*

### Expended Relationships (eager loading)
The response will include all the information of record linked to from another table.
In this example, with a single call, the field "Customer Details" will be filled with relations of "Customer Details" table.

When you don't pass an associative array, we assume that the field and the table name are the same.
```php
$expended = $airtable->getContent( "Customers/recpJGOaJYB4G36PU", false, [
    'Customer Details'
] );
```

If for some reasons the name of the field differs from the name of the table, you can pass an associative array instead.
```php
$expended = $airtable->getContent( "Customers/recpJGOaJYB4G36PU", false, [
    'Field Name' 	        => 'Table Name',
    'Customer Meetings'  => 'Meetings'
] );
```

We heard you like to expend your relationships, so now you can expend your expended relationships.
The following is possible.
```php
$expend_expended = $airtable->getContent( "Customers/recpJGOaJYB4G36PU", false, [
    'Customer Details',
    'Meetings'      => [
        'table'     => 'Meetings',
        'relations' => [
            'Calendar'  => 'Calendar',
            'Door'      => [
                'table'         => 'Doors',
                'relations'     => [
                    'Added By'  => 'Employees'
                ]
            ]
        ]
    ]
] );
```

But be aware that loading too many relationships can increase the response time considerably.

### Delete entry
Use the entry ID to delete the entry
```php
$delete_contact = $airtable->deleteContent("Contacts/{entry-id}");
```
*Batch Delete now available, documentation available below*

### Quick Check (new)
Find a record or many with one line. It's useful when you want to know if a user is already registered or if the same SKU is used.
The response will return "count" and "records".
```php
$check = $airtable->quickCheck("Contacts",$field,$value);

$check = $airtable->quickCheck("Contacts","Email","jon@wordlco.com");
if($check->count > 0){
    // the value is already there
    var_dump($check->records);
} else {
    // it's not there
}
```

### Batch Create, Update, Delete
Airtable API now allows to create, update, and delete 10 records per API request.

Create
```php
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
```

Update
```php
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
```

Delete
```php
$delete = [];

foreach ( $response->records as $record )
{
    $delete[] = $record->id;
}

$response = $a->deleteContent( 'Links', $delete );
```

## Credits

Copyright (c) 2019 - Programmed by Sleiman Tanios & Guillaume Laliberté
