<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class UsersController extends Controller {

    public function indexAction() {
        $request = new Request();
        $request = Helper::validateRequest( $request );
    }

    public function validateAction() {
        $request = new Request();

        if ( true === $request->isPost() ) {

            if ( ! empty( $request->get('userData') ) ) {
                $formData = $request->get( 'userData' );
            } else {
                $formData = array();
            }

            $operation = 'User';
            $helper    = new Helper();

            // Check for empty values.
            foreach ( $formData as $key => $input ) {
                // Required fields that must be numeric.
                if ( empty( $input ) ) {
                    $error     = 'Bad Request. ' . $key . ' is not be empty';
                    $errorCode = 400;
                    break;
                } else {
                    $error     = false;
                    $errorCode = false;
                    $formData[ $key ] = $helper->sanitize( $input );
                }
            }

            if ( false === $error ) {
                $checkDuplicateData = array( 
                    'email' =>  $formData['email'],
                    'username'  =>  $formData['username'],
                );

                $dupResult = $this->checkIfUserExists( $checkDuplicateData );
                if ( false !== $dupResult ) {
                    $error = 'User already exists. Try a different ' . $dupResult . '.';
                    $errorCode = 409;
                }
            }
            
            // Data is correct.
            if ( false === $error ) {
                // Updating User.
                if ( ! empty( $formData['id'] ) ) {
                    try {
                        // Save and create new order now.
                        $user = new Users();

                        // Assign value from the form to $user.
                        $user->assign(
                            $formData,
                            [
                                'id',
                                'fname',
                                'lname',
                                'username',
                                'email',
                                'password',
                                'phone',
                                'role'
                            ]
                        );

                        $user->save();

                    } catch (\Throwable $th) {
                        $helper = new Helper();
                        $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                        return;
                    }

                    $successCode = 200;
                    $message     = 'Selected User updated';
                    $id          = $user->id;
                } else {
                    try {
                        // Save and create new order now.
                        $user = new Users();
                        // Assign value from the form to $user.
                        $user->assign(
                            $formData,
                            [
                                'fname',
                                'lname',
                                'username',
                                'email',
                                'password',
                                'phone',
                                'role',
                            ]
                        );

                        $user->save();

                    } catch (\Throwable $th) {

                        throw $th;
                        $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                        return;
                    }

                    $successCode = 200;
                    $message     = 'New User added';
                    $id          = $user->id;
                }
            }

            if ( ! empty( $error ) ) {
                $helper->sendErrorReport( $errorCode, $error, $operation );
            } elseif ( ! empty( $message ) ) {
                $helper->sendSuccessReport( $successCode, $message, $operation, $id );
            }
        
        } else {
            $this->response->redirect( '/register' );
        }
    }

    public function checkIfUserExists( $checkDuplicateData = array() ) {

        if ( empty( $checkDuplicateData ) ) return false;
        
        $result = false;
        foreach ( $checkDuplicateData as $meta_key => $meta_value ) {

            if ( ! empty( Users::get( compact( 'meta_key', 'meta_value' ) ) ) ) {
                $result = $meta_key;
                break;
            }
        }

        return $result;
    }
}