<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Loader;

class AddProductController extends Controller {
    public function indexAction() {
    }

    public function validateAction() {
        $request = new Request();
        if ( true === $request->isPost() ) {

            if ( ! empty( $request->get('productData') ) ) {
                $formData = $request->get( 'productData' );
                $operation = 'product';
            } elseif ( $request->get('orderData') ) {
                $formData = $request->get( 'orderData' );
                $operation = 'order';
            } else {
                $formData = array();
                $operation = false;
            }
            
            $helper = new Helper();

            // Validate the data and check if data is valid then add the product else send a response 401.
            switch ($operation) {
                case 'product':
                    foreach ( $formData as $key => $input ) {
                        // Required fields that must be numeric.
                        if ( in_array( $key, array( 'price', 'stock' ) ) && ! is_numeric( $input ) ) {
                            $error     = 'Invalid Datatype. Numeric value required for field ' . $key;
                            $errorCode = 401;
                            break;
                        } else {
                            $error     = false;
                            $errorCode = false;
                            $formData[ $key ] = $helper->sanitize( $input );
                        }
                    }

                    // Search for the product already exists.
                    $searchResults = Products::find(
                        [
                            'conditions' => 'name = :p_name:',
                            'bind'       => [
                                'p_name' => $formData['name'],
                            ]
                        ]
                    );

                    // Already exists.
                    if ( count( $searchResults ) > 0 ) {
                        foreach ( $searchResults as $key => $product ) {
                            break;
                        }
                        
                        $successCode = 409;
                        $message     = 'Product already exists with same name. Try adding a different name or updating the product.';
                        $id          = $product->id;
                        $helper->sendSuccessReport( $successCode, $message, $operation, $id );
                        return;
                    }

                    if ( false === $error ) {

                        try {

                            // Save and create new product now.
                            $product = new Products();

                            // Assign value from the form to $user.
                            $product->assign(
                                $formData,
                                [
                                    'name',
                                    'price',
                                    'tag',
                                    'stock',
                                    'description',
                                ]
                            );

                            $product->save();

                        } catch (\Throwable $th) {
                            $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                            return;
                        }

                        $successCode = 200;
                        $message     = 'New Product added';
                        $id          = $product->id;
                    }
                    break;

                case 'order':
                    $errorCode = 404;
                    $error     = 'Invalid Operation Performed';
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