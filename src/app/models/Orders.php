<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`orders` ( `cust_name` VARCHAR(50) NOT NULL , `cust_addr` VARCHAR(500) NOT NULL , `cust_zipcode` INT(10) NOT NULL , `line_items` VARCHAR(5000) NOT NULL ) ENGINE = InnoDB;
class Orders extends Model
{
    public $id;
    public $cust_name ;
    public $cust_addr;
    public $cust_zipcode;
    public $line_items;
    public $status;

    const PRIMARY = array( 
        'id',
    );

    const REQUIRED = array( 
        'cust_name' => 'any',
        'cust_addr' => 'any',
        'cust_zipcode' => 'numeric',
        'line_items' => 'array',
        'status' => array (
            'pending',
            'processing',
            'completed',
            'refunded',
            'cancelled',
        )
    );

    // Search for single or all orders.
    public static function get( array $meta = array(), string $search = '', bool $returnResult = false ) {

        $args =  [
            'conditions' => $meta[ 'meta_key' ] . ' = :meta_value: ',
            'bind'       => [
                'meta_value' => $meta['meta_value'],
            ]
        ];

        $searchResults = self::find( $args );

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $order ) {
                break;
            }

            if( true === $returnResult ) return $order;
            $order->line_items = self::formatLineItems( $order->line_items );
            return HttpManager::formatResponse( $order, 'Orders' ) ?? false;
        } else {
            $err = new Exception( 'No results found with this request', 404 );
            return HttpManager::sendErrResponse( $err );
        }
    }

    public function createNew( array $request, array $body ) {
        if ( ! empty( $body ) ) {

            // check of required and supported values.
            $this->checkForRequiredParams( $body );

            // Create new order and then attach a api key with created order id.
            try {

                $body['line_items'] = serialize( $body['line_items'] );

                // Assign value from the form to $order.
                $this->assign(
                    $body,
                    [
                        'cust_name',
                        'cust_addr',
                        'cust_zipcode',
                        'line_items',
                        'status',
                    ]
                );

                $this->save();

                if ( empty( $this->id ) ) {
                    $th = new Exception( 'Bad Request : Order creation failed. Check your request again.', 403 );
                    HttpManager::sendErrResponse( $th );
                }

            } catch (\Throwable $th) {
                HttpManager::sendErrResponse( $th );
            }

            HttpManager::formatResponse( $this, 'Orders' );

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

                // check of required and supported values.
                $this->checkForRequiredParams( $body );
                $keyToBeUpdated = array( 
                    'id',
                    'cust_name',
                    'cust_addr',
                    'cust_zipcode',
                    'line_items',
                    'status',
                );
                break;
        }


        if ( ! empty( $body['line_items'] ) ) {

            foreach ( $body['line_items'] as $key => $item ) {

                if( empty( $item->id ) ) {
                    $err = new Exception( 'Empty product Id passed. Please check again', 403 );
                    HttpManager::sendErrResponse( $err );
                };

                $object = Products::findFirst('id = ' . $item->id );
                if ( empty( $object ) ) {
                    $err = new Exception( 'Invalid product Id passed. Please check again', 403 );
                    HttpManager::sendErrResponse( $err );
                }
            }
            $body['line_items'] = serialize( $body['line_items'] );
        }

        if( ! empty( $result ) ) {
            $result->assign(
                $body,
                $keyToBeUpdated
            );

            $result->save();

            $result->line_items = self::formatLineItems( $result->line_items );
            HttpManager::formatResponse( $result, 'Orders' );
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
        foreach ( self::REQUIRED as $key => $structure ) {

            if ( empty( $body[$key] ) ) {
                $err = new Exception( 'Required value not found in request. Add field name : ' . $key, 400 );
                HttpManager::sendErrResponse( $err );
            } else {
                $value = $body[$key];

                switch ($key) {
                    case 'cust_zipcode':
                        if ( ! is_numeric( $value ) ) {
                            $err = new Exception( 'Unsupported value found in request. Add numeric value in field name : ' . $key, 401 );
                        }
                        break;

                    case 'status':
                        if ( ! in_array( $value, $structure ) ) {
                            $err = new Exception( 'Unsupported value found in request. Add possible values ( ' . implode( ', ', $structure ) . ' ) in field name : ' . $key, 401 );
                        }
                        break;

                        case 'line_items':
                            foreach ( $value as $k => $api_object ) {
                                if ( empty( $api_object->id ) || empty( $api_object->quantity ) ) {
                                    $err = new Exception( 'Required value found in request. Add missing ID/Quantity in line item : ' . $k, 401 );
                                }

                                $object = Products::findFirst('id = ' . $api_object->id );

                                if ( empty( $object ) ) {
                                    $err = new Exception( 'Invalid product Id passed. Please check again', 403 );
                                }
                            }
                        break;
                }

                if ( ! empty( $err ) ) {
                    HttpManager::sendErrResponse( $err );
                }
            }
        }

        return true;
    }

    public static function formatLineItems( string $line_items ) {
        $line_items = unserialize( $line_items );
        $formatted_line_items = array();

        if ( ! empty( $line_items ) ) {

            foreach ( $line_items as $key => $item ) {

                if( empty( $item->id ) ) continue;

                $object = Products::findFirst('id = ' . $item->id );
                $line_item = array(
                    'id'       =>  $item->id,
                    'name'     =>  $object->name,
                    'quantity' =>  $item->quantity
                );

                $formatted_line_items[] = $line_item;
            }
        }

        return $formatted_line_items;
    }
}