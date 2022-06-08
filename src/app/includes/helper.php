<?php
use Phalcon\Escaper;
use Phalcon\Http\Response;

/**
 * This class file contains core functions that will be used for plugin nature validations.
 */
class Helper {

    public function sanitize( string $input = '' ) {
        $escaper = new Escaper();
        return $escaper->escapeHtmlAttr($input);
    }

    public function sendErrorReport( int $code, string $error, string $object ) {
        // Getting a response instance.
        $response = new Response();
        $response->setStatusCode($code, 'Something Went Wrong');
        $contents = [
            $object => compact( 'code', 'error' )   
        ];
        
        $response->setJsonContent($contents)->send();
    }

    public function sendSuccessReport( int $code, string $message, string $object, int $id = 0 ) {
        // Getting a response instance.
        $response = new Response();
        $contents = [
            $object => compact( 'id', 'code', 'message' )
        ];
        
        $response->setJsonContent($contents)->send();
    }

    public function getProduct( int $id = 0 ) {
        $searchResults = Products::find(
            [
                'conditions' => 'id = :p_id:',
                'bind'       => [
                    'p_id' => $id,
                ]
            ]
        );

        // Already exists.
        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $product ) {
                break;
            }
            return $product->name;
        }
        return false;
    }
}
