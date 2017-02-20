# Airtable PHP Wrapper
A PHP wrapper for the Airtable API. Feedback or bug reports are appreciated.


## Get started

Please note that Airtable doesn't allow schema manipulation using their public API, you have to create your tables using their interface.

Once you created your base in the Airtable Interface open the API Docs to get your Base ID.


<img src="example/img/api-doc-b.png" alt="API Doc Airtable"  width="350">

The Base ID is a code that starts with 'app' followed by a mix of letter or numbers (appsvqGDFCwLC3I10).

---

### Installation

```
include('Airtable.php');
```

### Initialize the class

```php
use \TANIOS\Airtable\Airtable;
$airtable = new Airtable(array(
	'api_key'=> 'API_KEY',
	'base'   => 'BASE_ID'
));
```


### Get all entries in table
We are getting all the entries from the table "Contacts". 
```php
$contacts = $airtable->getContent("Contacts");
print_r($contacts);
```

### Create new entry
We will create new entry in the table Contacts
```php
// Create an array with all the fields you want 
$new_contact_details = array(
	'Name' =>"Contact Name",
	'Address' => "1234 Street Name, City, State, Zip, Country",
	'Telephone #' => '123-532-1239',
	'Email' =>'email@domain.com',
);
// Save to Airtable
$new_contact = $airtable->saveContent("Contacts",$new_contact_details);
// The ID of the new entry
echo $new_contact->id;

print_r($new_contact);
```

### Update Contact
Use the entry ID to update the entry
```php
$update_contact_details = array(
	'Telephone #' => '514-123-2942',
);
$update_contact = $airtable->updateContent("Contacts/{entry-id}",$fields);
print_r($update_contact);
```

## Credits

Copyright (c) 2017 - Programmed by Sleiman Tanios