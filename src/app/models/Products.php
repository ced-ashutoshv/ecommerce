<?php
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

            $variations = array();
            foreach ( $product->variations as $key => $variation ) {
                foreach ( $variation as $meta_key => $meta_value ) {
                    $variations[] = '<strong>' . ucwords( str_replace( '_',' ',$meta_key ) ) . '</strong> : ' . ucwords( $meta_value ) . '';
                }
            }

            $products[]    = array(
                'id'         =>  $product->_id->__toString() ?? '',
                'name'       => $product->name,
                'category'   => $product->category,
                'price'      => $product->price,
                'stock'      => $product->stock,
                'meta'       => $meta,
                'variations' => $variations,
            );
        }

        return $products;
    }
}