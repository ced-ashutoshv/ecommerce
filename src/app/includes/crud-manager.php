<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
class CrudManager {

    protected $request;

    public function __construct( $request ){
        $this->request = $request;
    }

    /**
     * Perform GET Request for all models.
     */
    public function processGet() {
        
        $body    = self::prepareBody( $this->request['bodyObject'] );

        // We will get array as a body now.
        if ( ! empty( $body ) ) {
            
            $model = ucfirst(str_replace( '/', '', $this->request['uri'] )) ?? '';
            if( is_array( $body ) ) {
                $model::get( $body );
            } else {
                self::getAll( $model );
            }
        }
    }

    /**
     * Perform POST Request for all models.
     */
    public function processPost() {
        
        $body    = self::prepareBody( $this->request['bodyObject'] );
        // We will get array as a body now.
        if ( ! empty( $body ) ) {
            
            $model = ucfirst(str_replace( '/', '', $this->request['uri'] )) ?? '';
            if( is_array( $body ) ) {
                $model::get( $body );
            }
        }
    }

    /**
     * Check if body is valid or not and returns an array with same body.
     */
    public static function prepareBody( $body ) {

        // Decode the JSON data.
        $result = json_decode($body);
    
        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }
    
        if ( $error !== '' ) {
            // throw the Exception or exit // or whatever :)

            $error .= ' Check request body again.';
            $err = new Exception( $error, 500 );
            HttpManager::sendErrResponse( $err );
        }
        elseif ( new stdClass() == $result ) {
            return 'all';
        }

        // Everything is OK.
        return (array) $result;
    }

    public function getAll( string $model ) {
        $queryResult = $model::find([
            'order' => 'id'
        ]);

        if ( empty( $queryResult ) ) {
            $err = new Exception( 'No results found with this request', 404 );
            return HttpManager::sendErrResponse( $err );
        } else {
            $result = new stdClass();
            foreach ( $queryResult as $key => $user ) {
                $result->$key =  $user;
            }

            return HttpManager::formatResponse( $result, 'Users' ) ?? false;
        }
    }
}