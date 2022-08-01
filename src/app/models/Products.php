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

            
            // $variations = '<ul>';
            // if ( ! empty( $product->variations ) ) {
            //     echo '<pre>'; print_r( $product->variations ); echo '</pre>'; die;
            //     foreach ( $variation as $meta_key => $meta_value ) {
            //     }
            // }
            // $meta .= '</ul>';

            $products[]    = array(
                'id'         =>  $product->_id->__toString() ?? '',
                'name'       => $product->name,
                'category'   => $product->category,
                'price'      => $product->price,
                'stock'      => $product->stock,
                'meta'       => $meta,
                'variations' => '$variations',
            );
        }

        return $products;
    }

    public function validatedData( array $formData = array(), string $action = 'create' ) {
        $err = null;
        foreach ( $formData as $key => $value ) {
            switch ($key) {
                case 'id':
                    if ( 'update' === $action && empty( $formData['id'] ) ) {
                        $err = new Exception( 'Bad Request : Id field is mandatory for update', 400 );
                    }
                    break;
                case 'name':
                case 'category':
                    if ( empty( $formData[$key] ) ) {
                        $err = new Exception( 'Bad Request : ' . ucwords($key) . ' field is mandatory for update', 400 );
                    }
                    break;
                case 'price':
                case 'stock':
                    if ( empty( $formData[$key] ) ) {
                        $err = new Exception( 'Bad Request : ' . ucwords($key) . ' field is mandatory for update', 400 );
                    } elseif ( ! empty( $formData[$key] ) && ! is_numeric( $formData[$key] ) ) {
                        $err = new Exception( 'Bad Request : ' . ucwords($key) . ' field is not numeric', 400 );
                    }
                    break;
            }

            if ( ! empty( $err ) ) {
                break;
            }
        }

        if ( ! empty( $err ) ) {
            throw $err;
        }
    }

    public function prepareRequest( array $formData = array() ) {

        if ( array_key_exists( 'meta', $formData ) && ! empty( $formData['meta'] ) ) {
            $formData[ 'meta' ] = array_combine( $formData[ 'meta' ]['meta_key'], $formData[ 'meta' ]['meta_value'] );
        }

        return $formData;
    }

    public function createNew( array $formData = array() ) {

        $dbConnection = $this->di->get( 'db' );
        $collection   = $this->collection;
        $result       = $dbConnection->$collection->insertOne( $formData );
        return $result;
    }
}