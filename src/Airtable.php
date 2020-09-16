<?php

namespace TANIOS\Airtable;

/**
 * Airtable API Class
 *
 * @author Sleiman Tanios
 * @copyright Sleiman Tanios - TANIOS 2017
 * @version 1.0
 */

use Stiphle\Throttle;


class Airtable 
{
	
    const API_URL = "https://api.airtable.com/v0/";

    private $_key;

    private $_base;

    /**
     * @var Throttle\LeakyBucket
     */
    private $_throttle;
	
	public function __construct($config)
    {

        if (is_array($config)) {

            $config = array_merge( [

                'throttle'          => true,

            ], $config );

            $this->setKey($config['api_key']);
            $this->setBase($config['base']);

            if( ! empty( $config[ 'throttle' ] ) ) {

                $this->_throttle = new Throttle\LeakyBucket();
            }
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

    public function getApiUrl($request){
	    $request = str_replace( ' ', '%20', $request );
    	$url = self::API_URL.$this->getBase().'/'.$request;
    	return $url;
    }

    function getContent($content_type,$params="",$relations=false)
    {
        return new Request( $this, $content_type, $params, false, $relations );
	}

	function saveContent($content_type,$fields)
	{

	    if( ! $this->_detectBatch( $fields ) )
        {
            $fields = array('fields' => $fields);
        }
	    else
        {
            $fields = array('records' => $fields);
        }

		$request = new Request( $this, $content_type, $fields, true );

		return $request->getResponse();

	}

	function updateContent($content_type,$fields)
	{

        if( ! $this->_detectBatch( $fields ) )
        {
            $fields = array('fields' => $fields);
        }
        else
        {
            $fields = array('records' => $fields);
        }

		$request = new Request( $this, $content_type, $fields, 'patch' );

		return $request->getResponse();

	}

	function deleteContent($content_type, $records = null)
    {

        if( isset( $records ) && is_array( $records ) )
        {
            $fields = [
                'records'       => $records
            ];
        }
        else
        {
            $fields = [];
        }

        $request = new Request( $this, $content_type, $fields, 'delete' );

        return $request->getResponse();

    }
    
    function quickCheck($content_type,$field="",$value="")
    {
        $params = "";

        if (!empty($field)&& !empty($value)){

            $params = array(
                "filterByFormula" => 'AND({' . $field . '} = "' . $value .'")',
            );
        }
        
        $request = new Request( $this, $content_type, $params, false );

        $response = $request->getResponse();
        
     
        $results['count'] = isset( $response->records ) ? count($response->records) : 0;
        $results['records'] = $response->records;
        
     
        return (object)$results;
    }
    
    private function _detectBatch( $input )
    {

        if( is_array( $input ) )
        {
            $is_batch = false;

            foreach ( $input as $input_element )
            {
                if( is_array( $input_element ) && isset( $input_element[ 'fields' ] ) && is_array( $input_element[ 'fields' ] ) )
                {
                    $is_batch = true;
                    break;
                }
            }

            return $is_batch;
        }

        return false;

    }

    /**
     * @return Throttle\LeakyBucket
     */
    public function getThrottler()
    {

        return $this->_throttle;
    }

}
