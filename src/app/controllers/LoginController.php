<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class LoginController extends Controller {

    public function indexAction() {

        $user_id = $this->di->get( 'session' )->get( 'userid' );
        if ( ! empty( $user_id ) ) {
          $user = Users::findFirst( $user_id );
          $this->response->redirect( 'login/dashboard' );
        }
    }

    public function dashboardAction()
    {
        # code...
    }
    
    public function validateAction() {
        $request = new Request();

        if ( true === $request->isPost() ) {

            if ( ! empty( $request->get('loginData') ) ) {
                $formData = $request->get( 'loginData' );
            } else {
                $formData = array();
            }
        
            $helper = new Helper();

            $operation = 'User';
    
            foreach ( $formData as $key => $input ) {
    
                // Required fields that must be numeric.
                if ( empty( $input ) ) {
                    $error     = 'Bad Request. ' . $key . ' is not be empty';
                    $errorCode = 400;
                    break;
                } else {
                    $error     = false;
                    $errorCode = false;
                    if ( is_array( $input ) ){
                        $formData[ $key ] = $helper->sanitize( $input );
                    }
                }
            }
    
            if ( false === $error ) {

                // Check if this is email or username.
                if ( false === $this->isEmail( $formData[ 'email' ] ) ) {
                    $formData[ 'username' ] = $formData[ 'email' ];
                    unset( $formData[ 'email' ] );
                }

                if ( ! empty( $formData['username'] ) ) {
                    $user = Users::get( $formData, 'username' );
                } else {
                    $user = Users::get( $formData, 'email' );
                }

                if ( ! empty( $user ) ) {
                    
                    // Save in session.
                    $this->di->get( 'session' )->set( 'userid', $user->id );
                    $this->di->get( 'session' )->set( 'logged_in_status', 'true' );
                    $this->response->redirect( 'login' );
                } else {
                    $errorCode = 401;
                    $error = 'Credentials are not valid. Please try again.';
                }
            }
    
            // Send a api response.
            if ( ! empty( $error ) ) {
                $helper->sendErrorReport( $errorCode, $error, $operation );
            } elseif ( ! empty( $message ) ) {
                $helper->sendSuccessReport( $successCode, $message, $operation);
            }
        
        } else {

            $this->response->redirect( 'login' );
        }
    }

    public function logoutAction() {
        $this->di->get( 'session' )->destroy();
        $this->response->redirect( 'login' );
    }

    public function isEmail($email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

}