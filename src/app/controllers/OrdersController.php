<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class OrdersController extends Controller {

    public function indexAction() {
        $request = new Request();
        $request = HttpManager::parseRequest( $request );

        if ( ! empty( $request ) && 200 === $request['code'] ) {

            $api_key = $request['apiKey'] ?? false;
            if ( ! empty( $api_key ) ) {

                $crudManager = new CrudManager( $request );
                $method      = $request['method'] ?? 'GET';
                switch ( $method ) {
                    case 'POST':
                        $crudManager->processPost();
                        break;

                    case 'PUT':
                    case 'PATCH':
                        $crudManager->processPatch();
                        break;

                    case 'DELETE':
                        $crudManager->processDelete();
                        break;
                    
                    default:
                        $crudManager->processGet();
                        break;
                }
            }
        }
    }
}