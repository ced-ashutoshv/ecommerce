<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`products` ( `id` INT(10) NOT NULL , `name` VARCHAR(150) NOT NULL , `price` INT(5) NOT NULL , `description` VARCHAR(500) NOT NULL , `tag` VARCHAR(20) NOT NULL , `stock` VARCHAR(5) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

class Products extends Model
{
    public $id;
    public $name ;
    public $price;
    public $tag;
    public $stock;
    public $description;
}