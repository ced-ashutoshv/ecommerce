<?php
use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class EcommerceController extends Controller {
    public function indexAction() {
        
    }

    public function productAction(){

        $products = new Products();
        $products = $products->getAll() ?? array( '', 'No', 'Product', 'Found', 'in', 'this', 'Database' );
        $result   = array(
            'data'  =>  $products
        );
        echo json_encode( $result );
    }

    public function shopAction(){
    }

    public function ordersAction(){
    }
}