<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`orders` ( `cust_name` VARCHAR(50) NOT NULL , `cust_addr` VARCHAR(500) NOT NULL , `cust_zipcode` INT(10) NOT NULL , `line_items` VARCHAR(5000) NOT NULL ) ENGINE = InnoDB;
class Orders extends Model
{
    public $id;
    public $cust_name ;
    public $cust_addr;
    public $cust_zipcode;
    public $line_items;
    public $status;
}