<?php
use Phalcon\Escaper;
use Phalcon\Http\Request;
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
            $object => ! empty( $id ) ? compact( 'id', 'code', 'message' ) : compact( 'code', 'message' )
        ];
        
        $response->setJsonContent($contents)->send();
    }

    public function getProduct( $id = 0 ) {
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

    public static function sendErrResponse( $exception = false ) {

        if( ! $exception ) return;

        $code    = $exception->getCode();
        $message = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();
    
        $contents = compact( 'code', 'message', 'line', 'file' );
    
        $response = new Response();
        
        $response
            ->setJsonContent($contents, JSON_PRETTY_PRINT, 512)
            ->send();
        die;
    }

    public static function parseRequest( $request ){
        
        if ( empty( $request ) ) {
            $err = new Exception( "Error Processing Request", 401 );
            self::sendErrResponse( $err );
        }

        $code        = 200;
        $contentType = $request->getContentType();
        $bodyObject  = $request->getRawBody();
        $uri         = $request->getUri();
        $method      = $request->getMethod();
        $apiKey      = $request->getHeader( 'api_key' );
        $isSecure    = $request->isSecure();

        return compact( 'code', 'contentType', 'bodyObject', 'uri', 'method', 'apiKey', 'isSecure' );
    }
}
