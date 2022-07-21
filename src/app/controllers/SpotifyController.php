<?php
use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class SpotifyController extends Controller {
    
    const clientId = '755560cf36ad43278b7ad5b2333d408c'; 
    const secretId = 'b3eca3c8802944f7808dc373a41d3ee7'; 
    const baseUrl  = 'https://accounts.spotify.com'; 
    const authEndpoint = '/authorize'; 
    const tokenEndpoint = '/api/token';
    const redirectUri = 'http://localhost:8080/spotify/callback';

    public function indexAction() {
        $tokens = new Tokens();
        $this->view->tokens  = $tokens->find();
        $this->view->success = $this->request->get( 'success' );
        $this->view->t       = $this->request->get( 't' );..

        $token = $this->validateToken( $this->view->t );
    }

    public function callbackAction(){
        $request = $this->request->get();
        $state   = $request[ 'state' ] ?? '';
        if ( 'cedcommerce-auth' === $state ) {
            $code = $request['code'] ?? '';
            if ( empty( $code ) ) {
                $err = new Exception( 'Something Went Wrong. Code not found.', 400 );
                HttpManager::sendErrResponse( $err );
            } else {
                $tokenUrl = $url = self::baseUrl . self::tokenEndpoint;
                $curl     = curl_init();

                $postFields = array(
                    'grant_type'  =>  'authorization_code',
                    'code'  =>  $code,
                    'redirect_uri'  =>  self::redirectUri,
                );
                
                $postFields = http_build_query($postFields, '', '&');
                
                curl_setopt_array(
                    $curl, array(
                        CURLOPT_URL => $tokenUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $postFields,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Basic ' . base64_encode( self::clientId . ':' . self::secretId ),
                        ),
                    )
                );
                
                $response = curl_exec( $curl );
                curl_close($curl);
                $result = json_decode( $response, true );

                if ( ! empty( $result[ 'error' ] ) ) {
                    $err = new Exception( $result[ 'error_description' ], 500 );
                    HttpManager::sendErrResponse( $err );
                } else {
                    
                    $token = new Tokens();
                    $result['created_at'] = time();

                    $token->assign(
                        $result,
                        [
                            'access_token',
                            'token_type',
                            'expires_in',
                            'refresh_token',
                            'created_at',
                        ]
                    );

                    $token->save();

                    if( empty( $token->id ) ) {
                        $err = new Exception( 'Something Went wrong while saving the token', 400 );
                        HttpManager::sendErrResponse( $err );
                    } else {
                        $this->response->redirect( '/spotify?success=true' );
                    }
                }
            }
        } else {
            $err = new Exception( 'Basic authentication failed. Please check your state and retry again.', 400 );
            HttpManager::sendErrResponse( $err );
        }
    }

    public function initAction() {
        $data = array(
            'client_id' => self::clientId,
            'response_type' => 'code',
            'redirect_uri' => 'http://localhost:8080/spotify/callback',
            'state' => 'cedcommerce-auth',
            'scope' => 'user-modify-playback-state user-read-private user-read-email'
        );
        
        $url = self::baseUrl . self::authEndpoint .  '?' . http_build_query($data, '', '&');
        $this->response->redirect( $url );
    }

    public function validateToken( int $id = null ){

        $token = Tokens::findFirst( $id );
        $created_at = $token->created_at;
        $expired = ( $created_at + 3600 ) - time();
        if( $expired < 0 ) { 
          $expired = true;
        } else {
          $expired = false;
        }

        // Refresh Token.
        if ( true === $expired ) {
            $token = $this->refreshToken( $token );
        }

        return $token;
    }

    public function refreshToken( $token = null) {
        
        if ( ! empty( $token ) ) {
            $tokenUrl = $url = self::baseUrl . self::tokenEndpoint;
            $curl     = curl_init();
            $postFields = array(
                'grant_type'  =>  'refresh_token',
                'refresh_token'  =>  $token->refresh_token,
            );
            
            $postFields = http_build_query($postFields, '', '&');
            
            curl_setopt_array(
                $curl, array(
                    CURLOPT_URL => $tokenUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $postFields,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Basic ' . base64_encode( self::clientId . ':' . self::secretId ),
                    ),
                )
            );
            
            $response = curl_exec( $curl );
            curl_close($curl);
            $result = json_decode( $response, true );

            if ( ! empty( $result[ 'error' ] ) ) {
                
                // Delete this token.
                if ( 'Refresh token revoked' === $result['error_description'] ) {
                    $token->delete();
                }

                $err = new Exception( $result[ 'error_description' ], 500 );
                HttpManager::sendErrResponse( $err );
            } else {
                
                $result['created_at'] = time();

                $token->assign(
                    $result,
                    [
                        'access_token',
                        'token_type',
                        'expires_in',
                        'created_at',
                    ]
                );

                $token->save();

                if( empty( $token->id ) ) {
                    $err = new Exception( 'Something Went wrong while saving the token', 400 );
                    HttpManager::sendErrResponse( $err );
                }
            }
        }

        return $token;
    }
}