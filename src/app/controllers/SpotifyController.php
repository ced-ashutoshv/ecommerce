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
    const webApiUrl = 'https://api.spotify.com/v1';
    const scopes = array(
        'ugc-image-upload',
        'user-modify-playback-state',
        'user-read-playback-state',
        'user-read-currently-playing',
        'user-follow-modify',
        'user-follow-read',
        'user-read-recently-played',
        'user-read-playback-position',
        'user-top-read',
        'playlist-read-collaborative',
        'playlist-modify-public',
        'playlist-read-private',
        'playlist-modify-private',
        'app-remote-control',
        'streaming',
        'user-read-email',
        'user-read-private',
        'user-library-modify',
        'user-library-read',
    );
    const filters = array(
        'album',
        'artist',
        'playlist',
        'track',
        'show',
        'episode',
    );
    private $tokenId = false;

    public function indexAction() {
        $tokens = new Tokens();
        $this->view->tokens  = $tokens->find();
        $this->view->success = $this->request->get( 'success' );
        $this->view->t       = $this->request->get( 't' );

        $this->validateToken( $this->view->t );
        $this->tokenId            = $this->view->t;
        $token                    = Tokens::findFirst( $this->tokenId );
        $this->view->access_token = $token->access_token;
        $this->view->me           = $this->fetchMe();
        $user_id                  = $this->view->me['id'];
        $this->view->playlists    = $this->fetchPlaylists( $user_id );
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
            'scope' => implode( ' ', self::scopes )
        );

        $url = self::baseUrl . self::authEndpoint .  '?' . http_build_query($data, '', '&');
        $this->response->redirect( $url );
    }

    public function tracksAction() {

        $request    = new Request();
        $body       = $request->getRawBody();
        $playlistId = $request->getHeader( 'playlist-id' );
        $tracks  = json_decode( $body, true );
        if( ! empty( $tracks ) && ! empty( $tracks['items'] ) ) { ?>
            <table>
                <tr>
                    <th>No.</th>
                    <th>Thumbnail</th>
                    <th>Track Name</th>
                    <th>Play Externally</th>
                    <th>Remove</th>
                </tr>
                <?php foreach ( $tracks['items'] as $key => $track ) : ?>
                    <?php $track = $track['track'] ?? array(); ?>
                <tr>
                    <td><?php echo ++$key; ?></td>
                    <td><img class="track-img" src="<?php echo $track['album']['images'][0]['url']; ?>"></td>
                    <td><?php echo $track['name']; ?></td>
                    <td><a target="__blank" href="<?php echo $track['preview_url']; ?>"><img class="play-button" src="https://mwbdev13.nimbusweb.me/box/attachment/7299525/q517hmdlon7qs8xtfoxz/Y7yCTd7rMC6m9dV4/screenshot-www.freepik.com-2022.07.22-15_58_26.png"></a></td>
                    <td><span class="required" playlist-id="playlist-remove-<?php echo $playlistId; ?>" track-id="track-remove-<?php echo $track['id']; ?>">(X)</span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php
        } else {
            ?>
            <table>
                <tr>
                    <th colspan="3">No tracks found</th>
                </tr>
            </table>
            <?php
        }

        ?>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
                color : #ffffff;
                text-align : center;
            }
            .play-button {
                width : 30px;
            }
            .track-img {
                width:100px;
            }
            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
        </style>
        <?php
    }

    public function searchAction() {
        $this->view->t = $this->request->get( 't' );
        $this->validateToken( $this->view->t );
        $token                    = Tokens::findFirst( $this->view->t );
        $this->view->access_token = $token->access_token;
        $this->view->filters = self::filters;
    }

    public function resultAction() {
        $request             = new Request();
        $body                = $request->getRawBody();
        $this->view->result  = json_decode( $body, true );

        $view = new Phalcon\Mvc\View();

        $view->setVar('result', $this->view->result);

        $view->start();
        $view->render( 'spotify', 'result' );
        $view->finish();
        echo $view->getContent();
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

    public function doRequest( $url = '', $method = 'GET', $fields = array() ) {
        if ( ! empty( $this->tokenId ) ) {

            if ( ! empty( $fields ) ) {
                $postFields = http_build_query($fields, '', '&');
            }

            $access_token = Tokens::findFirst( $this->tokenId )->access_token;

            $curl     = curl_init();
            curl_setopt_array(
                $curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $postFields ?? array(),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: Content-Type: application/json',
                        'Authorization: Bearer ' . $access_token,
                    ),
                )
            );
            
            $response = curl_exec( $curl );
            curl_close($curl);
            $result = json_decode( $response, true );

            if ( ! empty( $result[ 'error' ] ) ) {
                
                // Delete this token.
                if ( 'Refresh token revoked' === $result['error_description'] ) {
                    $token = Tokens::delete( $this->tokenId );
                }

                $err = new Exception( $result[ 'error_description' ], 500 );
                HttpManager::sendErrResponse( $err );
            }

            else {
                return $result;
            }
        }
    }

    public function fetchMe() {
        $url = self::webApiUrl . '/me';
        return $this->doRequest( $url, 'GET' );
    }

    public function fetchPlaylists( $id = null ) {
        $url = self::webApiUrl . '/users/'. $id .'/playlists';
        $playlists = $this->doRequest( $url, 'GET' );
        // Items found.
        if ( $playlists[ 'total' ] > 0 || ! empty( $playlists[ 'items' ] ) ) {
            return $playlists['items'];
        } else {
            // No playlist found.
            return array();
        }


    }
}