<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`users` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `fname` VARCHAR(50) NOT NULL , `mname` VARCHAR(50) NOT NULL , `lname` VARCHAR(50) NOT NULL , `username` VARCHAR(10) NOT NULL , `email` VARCHAR(50) NOT NULL , `phone` VARCHAR(10) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
class Users extends Model
{
    public $id;
    public $fname;
    public $lname;
    public $username;
    public $email;
    public $password;
    public $phone;

    public static function get( array $meta = array() ) {
        $searchResults = Users::find(
            [
                'conditions' => $meta['meta_key'] . ' = :meta_value:',
                'bind'       => [
                    'meta_value' => $meta['meta_value'],
                ]
            ]
        );

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $user ) {
                break;
            }

            return $user ?? false;
        }
    }

}