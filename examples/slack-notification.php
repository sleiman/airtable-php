<?

require 'vendor/autoload.php';

use \TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key' => 'API_KEY',
    'base'    => 'BASE_ID',
    'slack_webhook' => 'SLACK_WEBHOOK_URL' // Get the URL here: https://api.slack.com/incoming-webhooks
));


$images_array = array(
	array(
	 	'url' => 'IMAGE_URL'
	)
);

$update_contact_details = array(
	'Image' => $images_array // test by changing the field name to something that doesn't exist
);

$update_contact = $airtable->updateContent("Customers/{entry-id}",$update_contact_details);

print_r($update_contact);