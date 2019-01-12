<?

require 'vendor/autoload.php';

use \TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key' => 'API_KEY',
    'base'    => 'BASE_ID',
));

/*
To add attachments to "Attachments", add new attachment objects to the existing array. Be sure to include all existing attachment objects that you wish to retain. To remove attachments, include the existing array of attachment objects, excluding any that you wish to remove.
*/

// Array of attachment objects (files, images)
$attachments_array = array(
	array(
	 	'url' => 'FIRST_ATTACHMENT_URL' // must be hosted on a web url 
	),
	array(
	 	'url' => 'SECOND_ATTACHMENT_URL' // must be hosted on a web url 
	)
);

$update_contact_details = array(
	'Attachments' => $attachments_array
);

$update_contact = $airtable->updateContent("Contacts/{entry-id}",$update_contact_details);

print_r($update_contact);