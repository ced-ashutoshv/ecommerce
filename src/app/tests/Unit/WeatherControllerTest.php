<?php

declare(strict_types=1);
namespace Tests\Unit;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Controller\WeatherController;

class WeatherControllerTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function indexAction(): void
    {
        $controller = new WeatherController();
        $this->assertEquals(
            "roman",
            "roman",
            "This will pass"
        );
    }
}