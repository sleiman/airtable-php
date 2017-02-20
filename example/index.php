<? 

include('../Airtable.php');

use TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key'      => 'API_KEY',
    'base'   => 'BASE_ID',
));

$donor = array(
	'Name' =>"John Brandon",
	'Address' => "1234 Street Name, City, State, Zip, Country",
	'Telephone #' => '123-123-1239',
	'Email' =>'email@domain.com',
);
$new_donor = $airtable->saveContent("Contacts",$donor);

echo 'New Donor ID: '.$new_donor->id;

if ($new_donor->id){

	$donation = array(
	'Amount of Donation' =>50,
	'Donor' => array($new_donor->id),
	'Type of Donation' => array('One-time'),
	'Tax Receipt Requested' =>true,
	'Date of Donation'=> date('Y-m-d')
	);
	$new_donation = $airtable->saveContent("Donations",$donation);
	echo 'New Donation ID: '.$new_donation->id;
}