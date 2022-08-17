<?php
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Di\DiInterface;
use Phalcon\Events\Manager;
use Phalcon\DI;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Controller\WeatherController;
use PHPUnit\Framework\TestCase;

class WeatherControllerTest extends TestCase {
    protected function setUp() : void {
        parent::setUp();
        $this->controller = new WeatherController();
    }
    public function test_that_index_action_works(): void {
        
        $_POST['q'] = 'india';
        $operation = $this->controller->indexAction();
        $expected = null;

        $this->assertEquals( $expected, $operation );
    }
}