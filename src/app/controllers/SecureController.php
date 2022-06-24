<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

// JWT
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class SecureController extends Controller {

    public function buildACLAction() {
        $aclFile = APP_PATH . '/security/acl.cache';

        if ( false == is_file( $aclFile ) || empty( file_get_contents( $aclFile ) ) ) {
            
            $acl = new Memory();
            $acl->addRole( 'admin' );
            $acl->addRole( 'manager' );
            $acl->addRole( 'customer' );
            $acl->addRole( 'guest' );

            $order_view   = new Component('order-list', 'Order View/edit');
            $product_view = new Component('product-list', 'Product View/edit');
            $order_add    = new Component('add-order', 'Order Add');
            $product_add  = new Component('add-product', 'Product Add');
            $settings  = new Component('settings', 'Settings');

            $acl->addComponent(
                $order_view,
                [
                    '*',
                ]
            );

            $acl->addComponent(
                $product_view,
                [
                    '*',
                ]
            );

            $acl->addComponent(
                $order_add,
                [
                    '*',
                ]
            );

            $acl->addComponent(
                $product_add,
                [
                    '*',
                ]
            );

            $acl->addComponent(
                $settings,
                [
                    '*',
                ]
            );


            $acl->allow( 'admin', 'order-list', '*' );
            $acl->allow( 'admin', 'product-list', '*' );
            $acl->allow( 'admin', 'add-order', '*' );
            $acl->allow( 'admin', 'add-product', '*' );
            $acl->allow( 'admin', 'settings', '*' );

            $acl->allow( 'manager', 'order-list', '*' );
            $acl->allow( 'manager', 'product-list', '*' );
            $acl->allow( 'manager', 'add-order', '*' );
            $acl->allow( 'manager', 'add-product', '*' );
            $acl->deny( 'manager', 'settings', '*' );

            $acl->allow( 'customer', 'add-order', '*' );

            $acl->deny( 'guest', '*', '*' );
 
            $result = file_put_contents(
                $aclFile,
                serialize( $acl )
            );

        } else {
            $acl = unserialize( file_get_contents( $aclFile ) );
        }

        echo '<pre>'; print_r( $acl ); echo '</pre>';
    }


    public function createAction(){

        // Defaults to 'sha512'
        $signer  = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        // Setup
        $builder
            ->setAudience('http://localhost:8080/')  // aud
            ->setContentType('application/json')        // cty - header
            ->setExpirationTime($expires)               // exp 
            ->setId('abcd123456789')                    // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject('My first Token ')   // sub
            ->setPassphrase($passphrase);                // password
        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token.
        echo $tokenObject->getToken(); die;

    }

    public function retrieveTokenAction() {
        # code...
    }
}