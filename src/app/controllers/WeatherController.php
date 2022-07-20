<?php
use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class WeatherController extends Controller {

    private $api_key   = '0bab7dd1bacc418689b143833220304';
    private $url       = 'http://api.weatherapi.com/v1/';
    private $endpoints = array(
        'forecast'   =>  'Forecast',
        'history'    =>  'History',
        'future'     =>  'Future',
        'sports'     =>  'Sports',
        'astronomy'  =>  'Astronomy',
        'airquality' =>  'Air Quality',
    );

    public function indexAction() {

        $this->view->endpoints = $this->endpoints;
        $request  = new Request();

        // Is a submission only.
        if (true === $request->isPost()) {
            
            // Validate if this is a valid country.
            $input = $request->get( 'q' );
            if ( ! empty( $input ) ) {
                $this->view->input = $input;
                
                try {
                    $report = self::fetch( 'current', $input );
                } catch (\Throwable $th) {
                    $this->view->err = $th->getMessage();
                    return;
                }
                if ( ! empty( $report ) ) {
                    $this->view->report = $report;
                } else {
                    $this->view->err = 'No result found with this country';
                }
            } else {
                $this->view->err = 'Please enter a valid Location';
            }
        }
    }

    public function requestAction() {
        $request  = new Request;
        $endpoint = $request->get( 'endpoint' ) ?? '';
        $input    = $request->get( 'input' ) ?? '';

        if ( ! empty( $endpoint ) && ! empty( $input ) ) {

            try {
                $report         = self::fetch( $endpoint, $input );
                $parsedResponse = self::parse( $report, $endpoint );

            } catch (\Throwable $th) {
                echo 'Error Occured : ' . $th->getMessage();
            }
        } else {
            echo 'Resource Not Found.';
        }

        die;
    }

    public function fetch( string $endpoint = null, string $input = null ) {

        $url = 'http://api.weatherapi.com/v1/' . $endpoint . '.json?q=' . $input;

        switch ( $endpoint)  {
            case 'history':
                $url .= '&dt=2022-07-18&end_dt=2022-07-20';
                break;
            
            case 'airquality':
                $url .= '&aqi=yes';
                $url = str_replace( 'airquality', 'current', $url );
                break;
            
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'key: ' . $this->api_key
          ),
        ));
        
        $response = json_decode( curl_exec($curl),true );
        curl_close($curl);

        if ( ! empty( $response['error'] ) ) {
            throw new Exception( $response['error']['message'], $response['error']['code'] );
        }

        return $response;
    }

    public function parse( array $report = null, string $endpoint = null ){
        if ( ! empty( $report ) ) {
            switch ($endpoint) {
                case 'forecast':
                    $response = $report[ $endpoint ];   
                    $forecast = $response['forecastday'][0] ?? array();
                    $today    = $forecast['day'] ?? array();
                    if ( ! empty( $today ) ) : ?>
                        <small>Max : <?php echo $today[ 'maxtemp_c' ]; ?>&#176;C</small><br>
                        <small>Min : <?php echo $today[ 'mintemp_c' ]; ?>&#176;C</small><br>
                        <small>Condition : <?php echo $today['condition'][ 'text' ]; ?></small><br>
                        <img src="<?php echo $today['condition'][ 'icon' ]; ?>">
                    <?php
                    endif;
                    break;
                
                case 'history':
                    $response = $report['forecast']['forecastday'][0]['hour'] ?? array();
                    if ( ! empty( $response ) ) : 
                        $response = array_slice( $response, 0, 5 );
                        foreach ( $response as $key => $date ) : ?>
                        <small><?php echo $date[ 'time' ]; ?> : <?php echo $date[ 'temp_c' ]; ?>&#176;C</small><br>
                        <?php
                        endforeach;
                    endif;
                    break;

                case 'sports':
                    $football = $report['football'] ?? array();
                    $cricket  = $report['cricket'] ?? array();
                    $golf     = $report['golf'] ?? array();

                    if ( ! empty( $football ) ) : ?>
                        <strong>FootBall</strong><br>
                        <?php
                        $football = array_slice( $football, 0, 5 );
                        foreach ( $football as $key => $match ) : ?>
                            <small><?php echo $match[ 'stadium' ]; ?> : <?php echo $match[ 'match' ]; ?></small>
                            <small><?php echo $match['start']; ?></small><br><br>
                        <?php
                        endforeach;
                    endif;
                    if ( ! empty( $cricket ) ) : ?>
                        <strong>Cricket</strong><br>
                        <?php
                        $cricket = array_slice( $cricket, 0, 5 );
                        foreach ( $cricket as $key => $match ) : ?>
                            <small><?php echo $match[ 'stadium' ]; ?> : <?php echo $match[ 'match' ]; ?></small>
                            <small><?php echo $match['start']; ?></small><br><br>
                        <?php
                        endforeach;
                    endif;
                    if ( ! empty( $golf ) ) : ?>
                        <strong>Golf</strong><br>
                        <?php
                        $golf = array_slice( $golf, 0, 5 );
                        foreach ( $golf as $key => $match ) : ?>
                            <small><?php echo $match[ 'stadium' ]; ?> : <?php echo $match[ 'match' ]; ?></small>
                            <small><?php echo $match['start']; ?></small><br><br>
                        <?php
                        endforeach;
                    endif;
                    break;

                case 'astronomy':
                    $response = $report['astronomy']['astro'] ?? array();
                    if ( ! empty( $response ) ) : 
                        foreach ( $response as $key => $value ) : ?>
                            <?php $key = str_replace( '_', ' ', $key ); ?>
                            <small><?php echo ucwords($key); ?> : <?php echo $value; ?></small><br>
                        <?php
                        endforeach;
                    endif;
                    break;

                case 'airquality':
                    $response = $report[ 'current' ]['air_quality'];
                    if ( ! empty( $response ) ) :
                        foreach ( $response as $key => $value ) : ?>
                            <?php $key = str_replace( '-', ' ', $key ); ?>
                            <small><?php echo ucwords($key); ?> : <?php echo $value; ?></small><br>
                        <?php
                        endforeach;
                    endif;
                    break;
                default:
                    # code...
                    break;
            }

        } else {
            throw new Exception( "Error Processing Request", 401 );
            
        }
    }
}