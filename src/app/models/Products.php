<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`products` ( `id` INT(10) NOT NULL , `name` VARCHAR(150) NOT NULL , `price` INT(5) NOT NULL , `description` VARCHAR(500) NOT NULL , `tag` VARCHAR(20) NOT NULL , `stock` VARCHAR(5) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

class Products extends Model
{
    public $id;
    public $name ;
    public $price;
    public $tag;
    public $stock;
    public $description;

    const PRIMARY = array( 
        'name',
    );

    const REQUIRED = array( 
        'name',
        'price',
        'tag',
        'stock',
        'description',
    );

    // Search for single or all products.
    public static function get( array $meta = array(), string $search = '', bool $returnResult = false ) {

        $args =  [
            'conditions' => $meta[ 'meta_key' ] . ' = :meta_value: ',
            'bind'       => [
                'meta_value' => $meta['meta_value'],
            ]
        ];

        $searchResults = self::find( $args );

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $product ) {
                break;
            }

            if( true === $returnResult ) return $product;
            return HttpManager::formatResponse( $product, 'Products' ) ?? false;
        } else {
            $err = new Exception( 'No results found with this request', 404 );
            return HttpManager::sendErrResponse( $err );
        }
    }

    public function createNew( array $request, array $body ) {
        if ( ! empty( $body ) ) {

            $this->checkForRequiredParams( $body );
            // Check if primary keys are already in database. If exists die!
            $this->restrictDuplicates( $body );

            // Create new product and then attach a api key with created product id.
            try {

                // Assign value from the form to $product.
                $this->assign(
                    $body,
                    [
                        'name',
                        'price',
                        'tag',
                        'stock',
                        'description',
                    ]
                );

                $this->save();

                if ( empty( $this->id ) ) {
                    $th = new Exception( 'Bad Request : Product creation failed. Check your request again.', 403 );
                    HttpManager::sendErrResponse( $th );
                }

            } catch (\Throwable $th) {
                HttpManager::sendErrResponse( $th );
            }

            HttpManager::formatResponse( $this, 'Products' );

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

        $method = $request['method'] ?? 'PATCH';

        switch ($method) {
            case 'PUT':
                $keyToBeUpdated = array_keys( $body );
                break;
            
            default:
                $keyToBeUpdated = array( 
                    'id',
                    'name',
                    'price',
                    'tag',
                    'stock',
                    'description',
                );
                break;
        }

        if( ! empty( $result ) ) {
            $result->assign(
                $body,
                $keyToBeUpdated
            );

            $result->save();

            HttpManager::formatResponse( $result, 'Products' );
        }
    }

    protected function restrictDuplicates( array $body ) {

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

    protected function checkForRequiredParams( array $body ) {
        foreach ( self::REQUIRED as $key => $value) {
            if ( empty( $body[$value] ) ) {
                $err = new Exception( 'Required value not found in request. Add field name : ' . $value, 400 );
                HttpManager::sendErrResponse( $err );
            }
        }
    }

}