<?php
use Phalcon\Escaper;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

/**
 * This class file contains core functions that will be used for plugin nature validations.
 */
class HttpManager {

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

    public static function formatResponse( Object $object, string $model ) {
        
        $response = ! empty( $object ) ? array( $model => get_object_vars($object) ) : '{}';
        $code    = 200;
        $time    = date('Y-m-d h:i:sa');
    
        $contents = compact( 'code', 'response', 'time' );
    
        $response = new Response();
        
        $response
            ->setJsonContent($contents, JSON_PRETTY_PRINT, 512)
            ->send();
        die;
    }
}
