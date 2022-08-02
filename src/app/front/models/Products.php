<?php

namespace Multi\Front\Models;
use Phalcon\Mvc\Model;

class Products extends Model {
    private $collection = 'products';
    private $_id;
    private $name;
    private $category;
    private $price;
    private $stock;
    private $meta;
    private $variations;

    public function getAll() {
        $dbConnection   = $this->di->get( 'db' );
        $collection     = $this->collection;
        $searchResults  = $dbConnection->$collection->find();
        $products       = array();
        foreach ( $searchResults as $key => $product ) {

            // Meta keys.
            $meta = '<ul>';
            foreach ( $product->meta as $meta_key => $meta_value ) {
                $meta .= '<li><strong>' . ucwords(str_replace('_',' ',$meta_key)) . '</strong> : ' . ucwords( $meta_value ) . '</li>';
            }
            $meta .= '</ul>';


            $products[]    = array(
                'id'         =>  $product->_id->__toString() ?? '',
                'name'       => $product->name,
                'category'   => $product->category,
                'price'      => $product->price,
                'stock'      => $product->stock,
                'meta'       => $meta,
                'variations' => 'Variations not enabled',
            );
        }

        return $products;
    }
}