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

}
