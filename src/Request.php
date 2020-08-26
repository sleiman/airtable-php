<?php
/**
 * Created by PhpStorm.
 * User: glali
 * Date: 2017-04-27
 * Time: 14:55
 */

namespace TANIOS\Airtable;

class Request implements \ArrayAccess
{

    /**
     * @var Airtable Instance of Airtable
     */
    private $airtable;
    /**
     * @var resource Instance of CURL
     */
    private $curl;
    /**
     * @var string Content type
     */
    private $content_type;
    /**
     * @var array Request data
     */
    private $data = [];
    /**
     * @var bool Is it a POST request?
     */
    private $is_post = false;

    /**
     * @var array|boolean Relations to lazy load
     */
    private $relations;

    /**
     * Create a Request to AirTable API
     * @param Airtable $airtable Instance of Airtable
     * @param string $content_type Content type
     * @param array $data Request data
     * @param bool|string $is_post Is it a POST request?
     */
    public function __construct( $airtable, $content_type, $data = [], $is_post = false, $relations = false )
    {

        $this->airtable = $airtable;
        $this->content_type = $content_type;
        $this->data = $data;
        $this->is_post = $is_post;
        $this->relations = $relations;

    }

    private function init()
    {

        $headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', $this->airtable->getKey())
        );

        $request = $this->content_type;

        $request_parts = explode( '/', $request );

        $request_parts = array_map( 'rawurlencode', $request_parts );

        $request = join( '/', $request_parts );

        $url_encoded = false;

        if( ! $this->is_post || strtolower( $this->is_post ) === 'delete' )
        {
            if (!empty($this->data)){
                $data = http_build_query($this->data);
                $request .= "?" . $data;
            }

            $url_encoded = true;
        }

        $curl = curl_init($this->airtable->getApiUrl($request));

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if( $this->is_post )
        {

            $url_encoded = false;

            if( strtolower( $this->is_post ) == 'patch' )
            {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            }
            else if( strtolower( $this->is_post ) == 'delete' )
            {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

                $url_encoded = true;
            }

            curl_setopt($curl,CURLOPT_POST, true);

            if( ! $url_encoded )
            {
                curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($this->data));
            }
        }

        $this->curl = $curl;

    }

    /**
     * @return Response Get response from API
     */
    public function getResponse()
    {

        $this->init();

        usleep( $this->getRateLimitWaitTime() * 1000 );

        $response_string = curl_exec( $this->curl );

        $response = new Response( $this->airtable, $this, $response_string, $this->relations );
            
        return $response;
               
    }

    public function __set( $key, $value )
    {
        if( ! is_array( $this->data ) )
        {
            $this->data = [];
        }

        $this->data[ $key ] = $value;
    }

    public function offsetExists($offset)
    {
        return is_array( $this->data) && isset( $this->data[ $offset ] );
    }

    public function offsetGet($offset)
    {
        return is_array( $this->data ) && isset( $this->data[ $offset ] )
            ? $this->data[ $offset ]
            : null;
    }

    public function offsetSet($offset, $value)
    {
        if( ! is_array( $this->data ) )
        {
            $this->data = [];
        }

        $this->data[ $offset ] = $value;
    }

    public function offsetUnset($offset)
    {
        if( is_array( $this->data ) && isset( $this->data[ $offset ] ) )
        {
            unset( $this->data[ $offset ] );
        }
    }

    private function getRateLimitWaitTime()
    {

        if( ( $throttler = $this->airtable->getThrottler() ) == null ) {

            return 0;
        }

        return $throttler->throttle( 'airtable', 5, 1000 );

    }

}