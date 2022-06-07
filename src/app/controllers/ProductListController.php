<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;

class ProductListController extends Controller {
    public function indexAction() {
        $query = $this->modelsManager->createQuery("SELECT * FROM Products");
        $this->view->products = $query->execute();
    }

    public function deleteAction() {

        $request = new Request();
        $pid     = $request->getQuery('pid');
        $searchResults = Products::find(
            [
                'conditions' => 'id = :p_name:',
                'bind'       => [
                    'p_name' => $pid,
                ]
            ]
        );

        foreach ( $searchResults as $key => $product ) {
            $product->delete();
        }
        
        $this->response->redirect( 'product-list/' );
    }
}