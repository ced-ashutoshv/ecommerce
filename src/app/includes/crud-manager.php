<?php
class CrudManager {

    public function processGet( $request ) {
        echo '<pre>'; print_r( $request ); echo '</pre>'; die;
    }


    public function fetchRole( string $apiKey = null ) {
        echo '<pre>'; print_r( $apiKey ); echo '</pre>'; die;
    }
}