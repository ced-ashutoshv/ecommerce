<?php

use Phalcon\Mvc\Model;

class Settings extends Model
{
    public $title;
    public $zipcode;
    public $price;
    public $stock;

    public static function get( string $meta = '' ) {
        $searchResults = Settings::find();

        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $settings ) {
                break;
            }
            if ( ! empty( $meta ) ) {
                return $settings->$meta ?? false;
            } else  {
                return $settings ?? false;
            }
        }
    }
}