<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

class SettingsController extends Controller {

    public function indexAction() {

        // Sync it to view.
        $this->view->settings = $this->getSettings();
    }

    public function getSettings() {
        $query = $this->modelsManager->createQuery( "SELECT * FROM Settings" );
        $searchResults = $query->execute();
        if ( count( $searchResults ) > 0 ) {
            foreach ( $searchResults as $key => $settings ) {
                break;
            }
        }

        return $settings ?? false; 
    }

    public function validateAction() {

        // Check if a new save is there.
        $request = new Request();

        if ( true === $request->isPost() ) {
            $formData = $request->getPost('settingsData');
            if ( ! empty( $formData ) ) {

                $operation = 'settings';
                $helper = new Helper();
                // Validate the data and check if data is valid then add the settings else send a response 401.
                switch ( $operation ) {
                    case 'settings':
                        foreach ( $formData as $key => $input ) {

                            // Required fields that must be numeric.
                            if ( empty( $input ) ) {
                                $error     = 'Bad Request. ' . $key . ' is not be empty';
                                $errorCode = 400;
                                break;
                            } elseif ( in_array( $key, array( 'price', 'stock', 'zipcode' ) ) && ( false === is_numeric( $input )) ) {
                                $error     = 'Invalid Datatype. Numeric value required for field ' . $key;
                                $errorCode = 401;
                                break;
                            }
                            else {
                                $error     = false;
                                $errorCode = false;
                                if ( is_array( $input ) ) {
                                    $formData[ $key ] = serialize( $input );
                                } else {
                                    $formData[ $key ] = $helper->sanitize( $input );
                                }
                            }
                        }

                        // Updating settings.
                        if ( false === $error ) {
                            try {

                                // Delete existing data.
                                $settings = $this->getSettings();

                                // Already saved settings
                                if ( false !== $settings ) {

                                    $formData['id'] = $settings->id;
                                    $settings->assign(
                                        $formData,
                                        [
                                            'id',
                                            'title',
                                            'price',
                                            'zipcode',
                                            'stock',
                                        ]
                                    );
                                    
                                } else { // New settings.

                                    // Save and create new settings now.
                                    $settings = new Settings();

                                    // Assign value from the form to $user.
                                    $settings->assign(
                                        $formData,
                                        [
                                            'title',
                                            'price',
                                            'zipcode',
                                            'stock',
                                        ]
                                    );
                                }

                                $settings->save();

                            } catch (\Throwable $th) {
                                $helper->sendErrorReport( '500', $th->getMessage(), $operation );
                                return;
                            }

                            $successCode = 200;
                            $message     = 'Settings updated';
                        }
                        break;
                }

                // Send a api response.
                if ( ! empty( $error ) ) {
                    $helper->sendErrorReport( $errorCode, $error, $operation );
                } elseif ( ! empty( $message ) ) {
    
                    $helper->sendSuccessReport( $successCode, $message, $operation);
                }
            }
        } else {
            $this->response->redirect('/');
        }
    }

}