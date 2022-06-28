<?php
use Phalcon\Http\Request;
use Phalcon\Http\Response;
class CrudManager {

    public function processGet( $request ) {
        echo '<pre>'; print_r( 'accessed get function perfectly.' ); echo '</pre>'; die;
    }

}