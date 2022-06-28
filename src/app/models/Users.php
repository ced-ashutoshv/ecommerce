<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`users` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `fname` VARCHAR(50) NOT NULL , `mname` VARCHAR(50) NOT NULL , `lname` VARCHAR(50) NOT NULL , `username` VARCHAR(10) NOT NULL , `email` VARCHAR(50) NOT NULL , `phone` VARCHAR(10) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
class Users extends Model {
    public $id;
    public $fname;
    public $lname;
    public $username;
    public $email;
    public $password;
    public $phone;
    public $role;
    public $api_key;

    const PRIMARY = array( 
        'email',
        'username',
    );

    const SEARCH = array( 
        'id',
        'email',
        'api_key',
        'username',
    );

    // Search for single or all users.
    public static function get( array $meta = array(), string $search = '', bool $returnResult = false ) {

        switch ($search) {
            case 'username':
                $args =  [
                    'conditions' => 'username = :username: AND password = :password:',
                    'bind'       => [
                        'username' => $meta['username'],
                        'password' => $meta['password'],
                    ]
                ];
                break;
            
            case 'email':
                $args =  [
                    'conditions' => 'email = :email: AND password = :password:',
                    'bind'       => [
                        'email' => $meta['email'],
                        'password' => $meta['password'],
                    ]
                ];
                break;

            default:
                $args =  [
                    'conditions' => $meta[ 'meta_key' ] . ' = :meta_value: ',
                    'bind'       => [
                        'meta_value' => $meta['meta_value'],
                    ]
                ];
                break;
        }

        $searchResults = self::find( $args );

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $user ) {
                break;
            }

            if( true === $returnResult ) return $user;
            return HttpManager::formatResponse( $user, 'Users' ) ?? false;
        } else {
            $err = new Exception( 'No results found with this request', 404 );
            return HttpManager::sendErrResponse( $err );
        }
    }

    public function createNew( array $request, array $body ) {
        if ( ! empty( $body ) ) {
            // Check if primary keys are already in database. If exists die!
            $this->restrictDuplicates( $body );

            // Create new user and then attach a api key with created user id.
            try {

                $body['api_key'] = rand();

                // Assign value from the form to $user.
                $this->assign(
                    $body,
                    [
                        'fname',
                        'lname',
                        'username',
                        'email',
                        'password',
                        'phone',
                        'role',
                        'api_key'
                    ]
                );

                $this->save();

                $body['api_key'] = AuthManager::create( $this->id, $this->role );
                $body['id']      = $this->id;

                $this->assign(
                    $body,
                    [
                        'id',
                        'fname',
                        'lname',
                        'username',
                        'email',
                        'password',
                        'phone',
                        'role',
                        'api_key'
                    ]
                );

                $this->save();

            } catch (\Throwable $th) {
                HttpManager::sendErrResponse( $th );
            }

            HttpManager::formatResponse( $this, 'Users' );

        }
    }

    public function updateExisting( array $request, array $body ) {

        if ( empty( $body['id'] ) ) {
            $err = new Exception( 'Required value not found in request. Add field name : id', 400 );
            HttpManager::sendErrResponse( $err );
        }

        $args = array(
            'meta_key' => 'id',
            'meta_value' => $body['id'],
        );
        
        $result = self::get( $args, '', true );

        if( ! empty( $result ) ) {
            $result->assign(
                $body,
                [
                    'fname',
                    'lname',
                    'username',
                    'email',
                    'password',
                    'phone',
                    'role',
                ]
            );

            $result->save();

            HttpManager::formatResponse( $result, 'Users' );
        }
    }

    public function restrictDuplicates( array $body ) {

        $exists = false;
        foreach ( self::PRIMARY as $k => $meta_key ) {
            if ( ! empty( $body[$meta_key] ) ) {
                $args =  [
                    'conditions' => $meta_key . ' = :meta_value: ',
                    'bind'       => [
                        'meta_value' => $body[$meta_key],
                    ]
                ];
                $result = self::find( $args );

                if ( count( $result ) > 0 ) {
                    $exists = $meta_key;
                    break;
                }
            } else {
                $err = new Exception( 'Required value not found in request. Add field name : ' . $meta_key, 400 );
                HttpManager::sendErrResponse( $err );
            }
        }
        
        if ( false !== $exists ) {
            $err = new Exception( 'Duplicate value encountered in request. Check field name : ' . $exists, 400 );
            HttpManager::sendErrResponse( $err );
        }

        return true;
    }
}