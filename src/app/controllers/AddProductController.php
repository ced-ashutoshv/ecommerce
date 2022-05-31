<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

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

            
        }
    }
}