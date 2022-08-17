<?php

declare(strict_types=1);

namespace Tests\Unit;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Incubator\Test\PHPUnit\UnitTestCase;
use PHPUnit\Framework\IncompleteTestError;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;

define( 'APP_PATH', '/var/www/html/app/' );

abstract class AbstractUnitTest extends UnitTestCase
{
    private bool $loaded = false;

    protected function setUp(): void
    {
        parent::setUp();
        $di = new FactoryDefault();
        $di->set(
            'loader',
            function () {
                $loader = new Loader();
                return $loader;
            }
        );
        $loader = $di['loader'];
        $loader->registerDirs(
            [
                '/var/www/html/app/controllers/',
                '/var/www/html/app/models/',
            ]
        );

        $loader->register();
        $this->setDi($di);
        $this->loaded = true;
    }

    public function __destruct()
    {
        if (!$this->loaded) {
            throw new IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }
}