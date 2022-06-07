<?php

use Phalcon\Mvc\Controller;

class ProductListController extends Controller {
    public function indexAction() {
        $query = $this->modelsManager->createQuery("SELECT * FROM Products");
        $this->view->products = $query->execute();
    }
}