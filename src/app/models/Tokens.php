<?php

use Phalcon\Mvc\Model;

// CREATE TABLE `ecommerce`.`tokens` ( `id` INT(100) NOT NULL AUTO_INCREMENT , `access_token` VARCHAR(500) NOT NULL , `token_type` VARCHAR(10) NOT NULL , `expires_in` INT(10) NOT NULL , `refresh_token` VARCHAR(100) NOT NULL , `created_at` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
class Tokens extends Model
{
    public $id;
    public $access_token;
    public $token_type;
    public $expires_in;
    public $refresh_token;
    public $created_at;
}