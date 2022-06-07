<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;

class OrderListController extends Controller {
    public function indexAction() {
        $query = $this->modelsManager->createQuery("SELECT * FROM Orders");
        $this->view->orders = $query->execute();
    }

    public function deleteAction() {

        $request = new Request();
        $o_id     = $request->getQuery('o_id');
        $searchResults = Products::find(
            [
                'conditions' => 'id = :o_id:',
                'bind'       => [
                    'o_id' => $o_id,
                ]
            ]
        );

        foreach ( $searchResults as $key => $order ) {
            $order->delete();
        }
        
        $this->response->redirect( 'order-list/' );
    }
}