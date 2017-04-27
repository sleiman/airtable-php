<?php
/**
 * Created by PhpStorm.
 * User: glali
 * Date: 2017-04-27
 * Time: 14:44
 */

namespace TANIOS\Airtable;


class Response implements \ArrayAccess
{

    /**
     * @var Airtable Instance of Airtable
     */
    private $airtable;

    /**
     * @var Request Instance or Request
     */
    private $request;

    /**
     * @var string Response content
     */
    private $content = "";

    /**
     * @var bool|\stdClass Response
     */
    private $parsedContent = false;

    /**
     * Response constructor.
     * @param Airtable $airtable Instance of Airtable
     * @param Request $request Instance of Request
     * @param string $content Content string
     */
    public function __construct( $airtable, $request, $content )
    {

        $this->airtable = $airtable;

        $this->request = $request;

        $this->content = $content;

        try
        {
            $this->parsedContent = json_decode( $content );
        }
        catch ( \Exception $e )
        {
            $this->parsedContent = false;
        }

    }

    public function next()
    {

        if( ! $this->parsedContent )
        {
            return false;
        }


        if( ! isset( $this[ 'offset' ] ) )
        {
            return false;
        }

        $this->request->offset = $this[ 'offset' ];

        return $this->request;

    }

    public function __get( $key )
    {
        if( ! $this->parsedContent || ! isset( $this->parsedContent->$key ) )
        {
            return null;
        }

        return $this->parsedContent->$key;
    }

    public function __toString()
    {
        return $this->content;
    }


    public function offsetExists($offset)
    {
        return $this->parsedContent && isset( $this->parsedContent->$offset );
    }


    public function offsetGet($offset)
    {
        return $this->parsedContent && isset( $this->parsedContent->$offset )
                ? $this->parsedContent->$offset
                : null;
    }

    public function offsetSet($offset, $value)
    {
        if( $this->parsedContent )
        {
            $this->parsedContent->$offset = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if( $this->parsedContent && isset( $this->parsedContent->$offset ) )
        {
            unset( $this->parsedContent->$offset );
        }
    }
}