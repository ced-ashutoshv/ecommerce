<?php
use Phalcon\Escaper;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

// JWT
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

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

    public function create( $attr = array() ){

        if ( empty( $attr ) ) {
            throw new Exception("Error Processing Request. Required attributes not found", 1);
        }

        // Defaults to 'sha512'
        $signer  = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        // Setup.
        $builder
            ->setAudience('http://localhost:8080/')     // aud
            ->setContentType('application/json')        // cty - header
            ->setExpirationTime($expires)               // exp 
            ->setId('abcd123456789')                    // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject('My first Token ')             // sub
            ->setPassphrase($passphrase);               // password
        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token.
        return $tokenObject->getToken();
    }

    public function validate() {

        $request = new Request();
        $tokenReceived = $request->get( 'token' );
        
        if ( empty( $token ) ) {
            throw new Exception("Error Processing Request. Token Not found in url", 1);
        }

        $audience      = 'http://localhost:8080/';
        $now           = new DateTimeImmutable();
        $issued        = $now->getTimestamp();
        $notBefore     = $now->modify('-1 minute')->getTimestamp();
        $expires       = $now->getTimestamp();
        $id            = 'abcd123456789';
        $issuer        = 'https://phalcon.io';
        
        // Defaults to 'sha512'
        $signer     = new Hmac();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        
        // Parse the token.
        $parser      = new Parser();
        
        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $parser->parse( $tokenReceived );
        
        // Phalcon\Security\JWT\Validator object
        $validator = new Validator($tokenObject, 100); // allow for a time shift of 100
        
        // Throw exceptions if those do not validate
        try {
            $validator
                ->validateAudience($audience)
                ->validateExpiration($expires)
                ->validateId($id)
                ->validateIssuedAt($issued)
                ->validateIssuer($issuer)
                ->validateNotBefore($notBefore)
                ->validateSignature($signer, $passphrase)
            ;
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}
