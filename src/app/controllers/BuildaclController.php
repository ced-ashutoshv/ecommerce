<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;


class BuildaclController {

    public function indexAction() {
        $aclFile = APP_PATH . '/security/acl.cache';

        if ( false == is_file( $aclFile ) || empty( file_get_contents( $aclFile ) ) ) {
            
            $acl = new Memory();
            $acl->addRole( 'admin' );
            $acl->addRole( 'manager' );
            $acl->addRole( 'customer' );
            $acl->addRole( 'guest' );

            $users = new Component('users', 'Users');
            $products = new Component('products', 'Products');

            $acl->addComponent(
                $users,
                [
                    '*',
                ]
            );

            $acl->addComponent(
                $products,
                [
                    '*',
                ]
            );


            // Users access.
            $acl->allow( 'admin', 'users', '*' );
            $acl->deny( 'manager', 'users', '*' );
            $acl->deny( 'customer', 'users', '*' );
            $acl->deny( 'guest', '*', '*' );

            // Products access.
            $acl->allow( 'admin', 'products', '*' );
            $acl->allow( 'manager', 'products', '*' );
            $acl->deny( 'customer', 'products', '*' );
            $acl->deny( 'guest', '*', '*' );
 
            $result = file_put_contents(
                $aclFile,
                serialize( $acl )
            );

        } else {
            $acl = file_get_contents( $aclFile );
        }

        return $acl ?? '';
    }
}