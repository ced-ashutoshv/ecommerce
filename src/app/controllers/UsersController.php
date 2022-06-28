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

                $crudManager = new CrudManager();
                $method      = $request['method'] ?? 'GET';
                switch ( $method ) {
                    case 'POST':
                        $response =  $crudManager->processPost( $request );
                        break;

                    case 'PUT':
                        $response =  $crudManager->processPut( $request );
                        break;

                    case 'PATCH':
                        $response =  $crudManager->processPatch( $request );
                        break;

                    case 'DELETE':
                        $response =  $crudManager->processDelete( $request );
                        break;
                    
                    default:
                        $response =  $crudManager->processGet( $request );
                        break;
                }
            }
        }
    }
}