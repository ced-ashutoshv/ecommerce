<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Http\Response;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

// Define some absolute path constants to aid in locating resources
define( 'BASE_PATH', dirname(__DIR__) );
define( 'APP_PATH', BASE_PATH . '/app' );
define( 'APP_SECRET', 'NFx5T1Tj5HyVfFarXxtORuAdidsKHuZGvhkjE4De6nZ0YcSDq0E7Xuh6fa2X7l3f' );

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

// Register some classes
$loader->registerFiles(
    [
        '../app/includes/http-manager.php',
        '../app/includes/query-manager.php',
        '../app/includes/crud-manager.php',
        '../app/includes/auth-manager.php',
        '../app/lib/vendor/autoload.php',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'db',
    function () {
        require '../app/etc/config.php';
        $config = new Config( $settings );
        $client = new MongoDB\Client(
            'mongodb+srv://' . $config->db->get( 'username' ) . ':' . $config->db->get( 'password' ) . '@' . $config->db->get( 'cluster' ) . '/?retryWrites=true&w=majority'
        );

        $db = $config->db->get( 'db_name' );
        return $client->$db;
    }
);

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;    
    }
);

$application = new Application($container);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {

    $code    = $e->getCode() * 100;
    $message = $e->getMessage();
    $file    = $e->getFile();
    $line    = $e->getLine();

    $contents = compact( 'code', 'message', 'line', 'file' );

    $response = new Response();
    
    $response
        ->setJsonContent($contents, JSON_PRETTY_PRINT, 512)
        ->send();
} 