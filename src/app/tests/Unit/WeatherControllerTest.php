<?php

declare(strict_types=1);
namespace Tests\Unit;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class WeatherControllerTest extends AbstractUnitTest {
    
    /**
     * @test
     */
    public function indexAction(): void {
        $controller = new \WeatherController();
        $controller->view = new View();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_REQUEST = array(
            'q' =>  'india'
        );
        $result = $controller->indexAction();
        $this->assertEquals(
            true,
            $result,
        );
    }
}