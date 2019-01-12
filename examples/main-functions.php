<? 

require 'vendor/autoload.php';

use TANIOS\Airtable\Airtable;

$airtable = new Airtable(array(
    'api_key'      => 'API_KEY',
    'base'   => 'BASE_ID',
));

// Get all entries from the table 'Deals' where the 'Status' is 'New'
$params = array(
    "filterByFormula" => "AND( Status = 'New' )"
);

$request = $airtable->getContent( 'Deals', $params);

do {
    $response = $request->getResponse();
    var_dump( $response[ 'records' ] );
}
while( $request = $response->next() );

print_r($request);


// Adding a new contact to the 'Contacts' table
$contact_details = array(
	'Name' =>"John Brandon",
	'Address' => "1234 Street Name, City, State, Zip, Country",
	'Telephone #' => '123-123-1239',
	'Email' =>'email@domain.com',
);
$new_contact = $airtable->saveContent("Contacts",$contact_details);


// Get the id after the entry is saved
echo 'New Contact ID: '.$new_contact->id;

// Save a new 'Payment' and link it to a 'Contact'
if ($new_contact->id){

	$payment_details = array(
		'Amount' =>50,
		'Contact' => array($new_contact->id), // array of ids to create a relationship
		'Type' => array('Purchase'), // single-select of multi-select
		'Date'=> date('Y-m-d')
	);
	$new_payment = $airtable->saveContent("Payments",$payment_details);
	echo 'Last Payment ID: '.$new_payment->id;
}
