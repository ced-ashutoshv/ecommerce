<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class IndexController {

    public function indexAction() {
        try {
            throw new Exception("Invalid Request Url", 404);
        } catch (\Throwable $th) {
            Helper::sendErrResponse( $th );
        }
    }
}