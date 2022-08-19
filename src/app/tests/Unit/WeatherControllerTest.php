<?php

declare(strict_types=1);
namespace Tests\Unit;
// use Phalcon\Di;
// use Phalcon\Mvc\Model\Manager as ModelsManager;
class WeatherControllerTest extends AbstractUnitTest {
    
    /**
     * @test
     */
    // public function indexAction(): void {
    //     $controller = new \WeatherController();
    //     $controller->view = new \Phalcon\Mvc\View();
    //     $_SERVER['REQUEST_METHOD'] = 'POST';
    //     $_REQUEST = array(
    //         'q' =>  'india'
    //     );
    //     $result = $controller->indexAction();
    //     $this->assertEquals(
    //         true,
    //         $result,
    //     );
    // }

    /**
     * @test
     */
    public function createTokenAction() : void {
        $token = $this->di->get( 'db' );
        echo '<pre>'; print_r( $token ); echo '</pre>'; die;
    }
}