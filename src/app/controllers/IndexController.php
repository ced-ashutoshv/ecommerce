<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class IndexController {

    public function indexAction() {

        AuthManager::createFirebaseAuth( 16 , 'admin' );

        try {
            throw new Exception("Invalid Request Url", 404);
        } catch (\Throwable $th) {
            HttpManager::sendErrResponse( $th );
        }
    }
}