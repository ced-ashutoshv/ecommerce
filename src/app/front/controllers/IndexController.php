<?php

namespace Multi\Front\Controllers;
use Phalcon\Mvc\Controller;

class IndexController extends Controller {
    public function indexAction() {
        $this->view->message = '<h1>HELLO THIS IS FRONT END</h1>';
    }
}