<?php

namespace Multi\Front\Controllers;
use Phalcon\Mvc\Controller;
use Multi\Front\Models\Products;

class ShopController extends Controller {
    public function indexAction() {
    }
    public function productsAction() {
        $dbConnection = $this->di->get('db');
        $products     = new Products();
        $products = $products->getAll() ?? array( '', 'No', 'Product', 'Found', 'in', 'this', 'Database' );
        $result   = array(
            'data'  =>  $products
        );
        echo json_encode( $result );
    }
}