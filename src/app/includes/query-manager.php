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
}
?>