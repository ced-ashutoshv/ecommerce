<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Loader;

class AddOrderController extends Controller {
    public function indexAction() {
        $request = new Request();
        $pid     = $request->getQuery('o_id');
        $searchResults = Orders::find(
            [
                'conditions' => 'id = :p_name:',
                'bind'       => [
                    'p_name' => $pid,
                ]
            ]
        );

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $order ) {
                break;
            }
            $this->view->order = $order;
        }

        $query = $this->modelsManager->createQuery("SELECT * FROM Products");
        $this->view->products = $query->execute();
    }

    public function validateAction() {
        $request = new Request();

        if ( true === $request->isPost() ) {

            if ( ! empty( $request->get('orderData') ) ) {
                $formData = $request->get( 'orderData' );
                $operation = 'order';
            } else {
                $formData = array();
                $operation = false;
            }

            $helper = new Helper();

            // Validate the data and check if data is valid then add the order else send a response 401.
            switch ( $operation ) {
                case 'order':
                    foreach ( $formData as $key => $input ) {

                        if ( 'id' === $key ) {
                            continue;
                        }

                        // Required fields that must be numeric.
                        if ( empty( $input ) ) {
                            $error     = 'Bad Request. ' . $key . ' is not be empty';
                            $errorCode = 400;
                            break;
                        } elseif ( in_array( $key, array( 'cust_zipcode' ) ) && ( false === is_numeric( $input )) ) {
                            $error     = 'Invalid Datatype. Numeric value required for field ' . $key;
                            $errorCode = 401;
                            break;
                        }
                        elseif ( in_array( $key, array( 'line_items' ) ) && ( false === is_array( $input ) ) ) {
                            $error     = 'Invalid Datatype. Array value required for field ' . $key;
                            $errorCode = 401;
                            break;
                        } else {
                            $error     = false;
                            $errorCode = false;
                            if ( is_array( $input ) ) {
                                $formData[ $key ] = serialize( $input );
                            } else {
                                $formData[ $key ] = $helper->sanitize( $input );
                            }
                        }
                    }

                    // Updating Order.
                    if ( ! empty( $formData['id'] ) ) {
                        if ( false === $error ) {
                            try {
                                // Save and create new order now.
                                $order = new Orders();

                                // Assign value from the form to $user.
                                $order->assign(
                                    $formData,
                                    [
                                        'id',
                                        'cust_name',
                                        'cust_addr',
                                        'cust_zipcode',
                                        'order_status',
                                        'line_items',
                                    ]
                                );

                                $order->save();

                            } catch (\Throwable $th) {
                                $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                                return;
                            }

                            $successCode = 200;
                            $message     = 'Selected Order updated';
                            $id          = $order->id;
                        }

                    } else { // Attempt creating new order.

                        if ( false === $error ) {

                            try {

                                // Save and create new order now.
                                $order = new Orders();

                                $formData['status'] = $formData['order_status'];
                                unset( $formData['order_status'] );

                                // Assign value from the form to $user.
                                $order->assign(
                                    $formData,
                                    [
                                        'cust_name',
                                        'cust_addr',
                                        'cust_zipcode',
                                        'status',
                                        'line_items',
                                    ]
                                );

                                $order->save();

                            } catch (\Throwable $th) {
                                $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                                return;
                            }

                            $successCode = 200;
                            $message     = 'New Order added';
                            $id          = $order->id;
                        }
                        
                    }
                    break;

                default:
                    $error     = 'Invalid Operation Performed';
                    $errorCode = 404;
                    break;
            }

            if ( ! empty( $error ) ) {
                $helper->sendErrorReport( $errorCode, $error, $operation );
            } elseif ( ! empty( $message ) ) {

                $helper->sendSuccessReport( $successCode, $message, $operation, $id );
            }
        } else {
            $this->response->redirect( '/' );
        }
    }
}