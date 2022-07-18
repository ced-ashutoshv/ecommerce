<?php
use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class WeatherController extends Controller {

    private $api_key   = '0bab7dd1bacc418689b143833220304';
    private $url       = 'http://api.weatherapi.com/v1/';
    private $endpoints = array(
        'current'   =>  'Current weather',
        'forecast'  =>  'Forecast',
        'search'    =>  'Search or Autocomplete',
        'history'   =>  'History',
        'future'    =>  'Future',
        'timezone'  =>  'Time Zone',
        'sports'    =>  'Sports',
        'astronomy' =>  'Astronomy',
        'ip'        =>  'IP Lookup',
    );

    public function indexAction() {
        $request  = new Request();

        // Is a submission only.
        if (true === $request->isPost()) {
            
            // Validate if this is a valid country.
            $input = $request->get( 'q' );
            if ( ! empty( $input ) ) {
                $this->view->input = $input;
                
                try {
                    $report = self::fetchWeather( $input );
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

    public function fetchWeather( string $input = null ) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://api.weatherapi.com/v1/current.json?q=' . $input,
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
}