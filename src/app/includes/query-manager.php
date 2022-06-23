<?php

use Phalcon\Http\Request;
use Phalcon\Http\Response;

/**
 * This class file contains core functions that will be used for plugin nature validations.
 */
class QueryManager {
    public function checkOrderData( $func, $managerObject = array(), $attr = array() ) {
        
        if ( ! empty( $attr ) && is_array( $attr ) ) {
            $attr[ 'cust_zipcode' ] = ! empty( $attr[ 'cust_zipcode' ] ) ? $attr[ 'cust_zipcode' ] : Settings::get('zipcode');
        }

        return $attr;
    }

    public function checkProductData( $func, $managerObject = array(), $attr = array() ) {
        
        if ( ! empty( $attr ) && is_array( $attr ) ) {

            $title_pattern = Settings::get( 'title' );

            switch ($title_pattern) {
                case 'with_tags':
                    $attr['name'] = $attr['name'] . ' ( ' . $attr['tag'] . ' ) ';
                    break;
            }

            $attr[ 'price' ] = ! empty( $attr[ 'price' ] ) ? $attr[ 'price' ] :  Settings::get('price');
            $attr[ 'stock' ] = ! empty( $attr[ 'stock' ] ) ? $attr[ 'stock' ] :  Settings::get('stock');
        }

        return $attr;
    }

    public function beforeHandleRequest( $func, $managerObject = array(), $attr = array() ) {

        $request = new Request();

        if ( in_array( $request->getURI(), array('/','/login','/register', '/secure/build_acl') ) ) {
            return;
        }

        $aclFile = APP_PATH . '/security/acl.cache';

        if( true === is_file( $aclFile ) ) {

            $acl = unserialize( file_get_contents( $aclFile ) );

            if( ! empty( $acl ) ) {
                $role = "admin";

                $request    = explode( '/', $request->getURI() );
                $controller = $request[1] ?? '';

                if ( empty( $role ) || $acl->isAllowed( $role, $controller, '*' ) ) {
                    // Do nothing.
                    
                } else {
                    // Getting a response instance
                    $response = new Response();

                    $response->setStatusCode(404, 'Not Found');
                    $response->setContent('<style>body{display:flex;align-items:center;justify-content:center;min-height:100vh;overflow:hidden;background-color:#eed9aa}#shakemessage{background-color:#c72f43;padding:15px 25px;border-radius:5px;color:#fff;font-family:"Patua One",cursive;font-size:3em;line-height:1em;text-align:center;text-transform:uppercase}#shakemessage span{color:rgba(255,255,255,.4);font-size:.4em;line-height:1.2em;display:block;margin-top:10px}a{text-decoration:none}</style><a href="/"><h1 id="shakemessage">ERROR 404<span>Sorry, the page doesn\'t exist or you might not have access to it.</span></h1></a>');
                    $response->send();
                    die;
                }
            } else {
                die( 'Configuration not found' );
            }
        }
    }
}
?>