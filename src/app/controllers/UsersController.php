<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class UsersController extends Controller {

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
                        $response =  $crudManager->processPost();
                        break;

                    case 'PUT':
                        $response =  $crudManager->processPut();
                        break;

                    case 'PATCH':
                        $response =  $crudManager->processPatch();
                        break;

                    case 'DELETE':
                        $response =  $crudManager->processDelete();
                        break;
                    
                    default:
                        $response =  $crudManager->processGet();
                        break;
                }
            }
        }
    }
}