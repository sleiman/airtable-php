<?

namespace TANIOS\Airtable;

/**
 * Airtable API Class
 *
 * @author Sleiman Tanios
 * @copyright Sleiman Tanios - TANIOS 2017
 * @version 1.0
 */


class Airtable 
{
	const API_URL = "https://api.airtable.com/v0/";

	private $_key;

    private $_base;
	
	public function __construct($config)
    {
        if (is_array($config)) {
            $this->setKey($config['api_key']);
            $this->setBase($config['base']);
        } else {
            echo 'Error: __construct() - Configuration data is missing.';
        }
    }

    public function setKey($key)
    {
        $this->_key = $key;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setBase($base)
    {
        $this->_base = $base;
    }

    public function getBase()
    {
        return $this->_base;
    }

    public function getApiUrl($content_type){
    	$url = self::API_URL.$this->getBase().'/'.$content_type;
    	return $url;
    }

    function getContent($content_type) 
    {
		$headers = array(
		'Content-Type: application/json',
		sprintf('Authorization: Bearer %s', $this->getKey())
		);

		$curl = curl_init($this->getApiUrl($content_type));

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		

		$result = json_decode(curl_exec($curl));

		return $result;
	}

	function saveContent($content_type,$fields)
	{
		$headers = array(
		'Content-Type: application/json',
		sprintf('Authorization: Bearer %s', $this->getKey())
		);
		$fields = array('fields'=>$fields);
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL, $this->getApiUrl($content_type));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_POST, count($fields));
		curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($fields));

		$result = json_decode(curl_exec($curl));
		return $result;

	}

	function updateContent($content_type,$fields)
	{
		$headers = array(
		'Content-Type: application/json',
		sprintf('Authorization: Bearer %s', $this->getKey())
		);
		$fields = array('fields'=>$fields);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl,CURLOPT_URL, $this->getApiUrl($content_type));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_POST, count($fields));
		curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($fields));

		$result = json_decode(curl_exec($curl));
		return $result;

	}

}