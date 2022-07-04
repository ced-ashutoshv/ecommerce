<?php

// Http
use Phalcon\Http\Request;
use Phalcon\Http\Response;

// JWT
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;


class AuthManager {

    public function beforeHandleRequest( $func, $managerObject = array(), $attr = array() ) {

        $request = new Request();

        $ignoredEndpoints = array(
            '/buildacl',
        );

        if ( in_array( $request->getUri(), $ignoredEndpoints ) ) {
            return true;
        }

        $aclFile = APP_PATH . '/security/acl.cache';

        if( true === is_file( $aclFile ) ) {

            $acl = unserialize( file_get_contents( $aclFile ) );

            if( ! empty( $acl ) ) {

                $request = HttpManager::parseRequest( $request );
                $api_key = $request['apiKey'] ?? false;

                if ( empty( $api_key ) ) {
                    $err = new Exception( "Authorization failed. Check your request headers again.", 403 );
                    HttpManager::sendErrResponse( $err );
                }

                $role = $this->doAuthCheck( $api_key );

                if ( empty( $role ) ) {
                    $err = new Exception( 'You don\'t have permission to access this resource', 301 );
                    HttpManager::sendErrResponse( $err );
                } else {
                    // Got a role now check if we have access for the request in acl.
                    $uri = $request['uri'] ? array_filter( explode( '/', $request['uri'] ) ) : '';
                    if ( ! empty( $uri ) && is_array( $uri ) ) {
                        $controller = $uri[1] ?? '';
                        if ( ! empty( $controller ) && $acl->isAllowed( $role, $controller, '*' ) ) {
                            return true;
                        } else {
                            $err = new Exception( 'You don\'t have enough permission to access this resource.', '301' );
                            HttpManager::sendErrResponse( $err );
                        }
                    } else {
                        return;
                    }
                }
            } else {
                $err = new Exception( 'Configuration not found', '301' );
                HttpManager::sendErrResponse( $err );
            }
        } else {
            $err = new Exception( 'Configuration File not found', '301' );
            HttpManager::sendErrResponse( $err );
        }
    }


    public function doAuthCheck( string $apiKey = null ) {

        $user_role = $this->validateApiKey( $apiKey ); // Returns role or bool ( false ).

        if ( false === $user_role ) {
            $err = new Exception( "Attached API Key in request is Expired or Incorrect", 403 );
            HttpManager::sendErrResponse( $err );
        }

        // Token Key is valid and we got the role now.
        return $user_role;
    }

    public static function create( $user_id = '', $role = '' ){

        // Defaults to 'sha512'
        $signer  = new Hmac();
        $user_id = $user_id ?? '';
        $role    = $role ?? '';

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
            ->setId($user_id)                           // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject($role)                         // sub
            ->setPassphrase($passphrase);               // password
        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token.
        return $tokenObject->getToken();
    }

    public function validateApiKey( $tokenReceived = false ) {
        
        if ( empty( $tokenReceived ) ) {
            $err = new Exception("Error Processing Request. Token Not found in request", 1);
            HttpManager::sendErrResponse( $err );
        }

        $searchResults = Users::find(
            [
                'conditions' => 'api_key = :api_key:',
                'bind'       => [
                    'api_key' => $tokenReceived,
                ]
            ]
        );

        // Already exists.
        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $user ) {
                break;
            }
        }

        if ( empty( $user ) ) {
            $err = new Exception( "No user found with attached API Key Not found in request" );
            HttpManager::sendErrResponse( $err );
        }

        $id = $user->id ?? '';

        $audience      = 'http://localhost:8080/';
        $now           = new DateTimeImmutable();
        $issued        = $now->getTimestamp();
        $notBefore     = $now->modify('-1 minute')->getTimestamp();
        $expires       = $now->getTimestamp();
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

        return $user->role;
    }

}