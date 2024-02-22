<?php
/**
 * Created by PhpStorm.
 * User: glali
 * Date: 2017-04-27
 * Time: 14:44
 */

namespace TANIOS\Airtable;


use ReturnTypeWillChange;

class Response implements \ArrayAccess
{

    /**
     * @var Airtable Instance of Airtable
     */
    private Airtable $airtable;

    /**
     * @var Request Instance or Request
     */
    private Request $request;

    /**
     * @var string Response content
     */
    private string $content = "";

    /**
     * @var bool|\stdClass Response
     */
    private mixed $parsedContent = false;

    /**
     * Response constructor.
     * @param Airtable $airtable Instance of Airtable
     * @param Request $request Instance of Request
     * @param string $content Content string
     */
    public function __construct( Airtable $airtable, Request $request, string $content, $relations = false )
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

        if( is_array( $relations ) && count( $relations ) > 0 )
        {

            if( array_keys( $relations ) !== range( 0, count( $relations ) - 1 ) )
            {
                foreach ( $relations as $related_field => $related_table )
                {
                    $this->processRelatedField( $related_field, $related_table );
                }
            }
            else
            {
                foreach ( $relations as $related_field )
                {
                    $this->processRelatedField( $related_field );
                }
            }

        }

    }

    private function processRelatedField( $related_field, $related_table = false )
    {

        if( isset( $this->parsedContent->records ) && is_array( $this->parsedContent->records ) && count( $this->parsedContent->records ) > 0 )
        {
            foreach ( $this->parsedContent->records as $record_key => $record )
            {
                $this->parsedContent->records[ $record_key ] = $this->loadRelatedField( $related_field, $related_table, $record );
            }
        }
        else
        {
            $this->parsedContent = $this->loadRelatedField( $related_field, $related_table, $this->parsedContent );
        }

    }

    private function loadRelatedField( $related_field, $related_table, $record )
    {


        if( ! isset( $record->fields ) || ! isset( $record->fields->$related_field ) )
        {
            return $record;
        }

        if( empty( $related_table ) )
        {
            $related_table = $related_field;
        }

        $relation_ids = $record->fields->$related_field;

        if( ! is_array( $relation_ids ) )
        {
            $relation_ids = [ $relation_ids ];
        }

        $relation_formula = "OR(";
        $relation_formula .= implode( ', ', array_map( function( $id ) { return "RECORD_ID() = '$id'"; }, $relation_ids ) );
        $relation_formula .= ")";

        if( ! is_array( $related_table ) )
        {
            $relation_request = $this->airtable->getContent( "$related_table", [
                'filterByFormula'       => $relation_formula
            ] );
        }
        else
        {
            $related_table_relations = isset( $related_table[ 'relations' ] ) && is_array( $related_table[ 'relations' ] )
                ? $related_table[ 'relations' ]
                : false;

            $related_table_name = ! empty( $related_table[ 'table' ] ) ? $related_table[ 'table' ] : $related_field;

            $relation_request = $this->airtable->getContent( "$related_table_name", [
                'filterByFormula'       => $relation_formula
            ], $related_table_relations );
        }


        $related_records = [];

        do
        {
            $relation_response = $relation_request->getResponse();

            if( ! is_array( $relation_response->records ) || count( $relation_response->records ) < 0 )
            {
                break;
            }

            foreach ( $relation_response->records as $related_record )
            {
                $formatted_record = $related_record->fields;
                $formatted_record->id = $related_record->id;

                $related_records[] = $formatted_record;
            }
        }
        while( $relation_request = $relation_response->next() );

        // Keep order
        if (count($relation_ids) > 1) {
            $related_records_sorted = array();

            foreach ($relation_ids as $relation_id) {
                $key = array_search($relation_id, array_column($related_records, 'id'));

                if ($key !== false) {
                    $related_records_sorted[] = $related_records[$key];
                }
            }

            $related_records = $related_records_sorted;
        }

        if( is_array( $record->fields->$related_field ) )
        {
            $record->fields->$related_field = $related_records;
        }
        else
        {
            $record->fields->$related_field = count( $related_records ) > 0
                ? $related_records[ 0 ]
                : null;
        }

        return $record;

    }

    public function next(): bool|Request
    {

        if( ! $this->parsedContent )
        {
            return false;
        }

        if( empty( $this[ 'offset' ] ) )
        {
            return false;
        }

        $this->request[ 'offset' ] = $this[ 'offset' ];

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

    public function __isset( $key )
    {
        return $this->parsedContent && isset( $this->parsedContent->$key );
    }

    #[ReturnTypeWillChange] public function offsetExists( $offset): bool
    {
        return $this->parsedContent && isset( $this->parsedContent->$offset );
    }

    #[ReturnTypeWillChange] public function offsetGet( $offset)
    {
        return $this->parsedContent && isset( $this->parsedContent->$offset )
                ? $this->parsedContent->$offset
                : null;
    }

    #[ReturnTypeWillChange] public function offsetSet( $offset, $value)
    {
        if( $this->parsedContent )
        {
            $this->parsedContent->$offset = $value;
        }
    }

    #[ReturnTypeWillChange] public function offsetUnset( $offset)
    {
        if( $this->parsedContent && isset( $this->parsedContent->$offset ) )
        {
            unset( $this->parsedContent->$offset );
        }
    }
}