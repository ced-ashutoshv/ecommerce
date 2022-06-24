<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;



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
}