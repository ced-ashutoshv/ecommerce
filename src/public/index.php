<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\Loader;
use Phalcon\Config;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

// Register some classes
$loader->registerFiles(
    [
        '../app/lib/vendor/autoload.php',
    ]
);

$loader->register();

$container = new FactoryDefault();

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
    'router',
    function () {
        $router = new Router();

        $router->setDefaultModule('front');

        $router->add(
            '/',
            [
                'module'     => 'front',
                'controller' => 'shop',
                'action'     => 'index',
            ]
        );

        $router->add(
            '/products',
            [
                'module'     => 'front',
                'controller' => 'shop',
                'action'     => 'products',
            ]
        );


        $router->add(
            '/admin',
            [
                'module'     => 'back',
                'controller' => 'admin',
                'action'     => 'index',
            ]
        );

        $router->add(
            '/admin/products/:action',
            [
                'module'     => 'back',
                'controller' => 'products',
                'action'     => 1,
            ]
        );

        $router->add(
            '/products/:action',
            [
                'controller' => 'products',
                'action'     => 1,
            ]
        );

        return $router;
    }
);

$application = new Application($container);

$application->registerModules(
    [
        'front' => [
            'className' => \Multi\Front\Module::class,
            'path'      => '../app/front/Module.php',
        ],
        'back'  => [
            'className' => \Multi\Back\Module::class,
            'path'      => '../app/back/Module.php',
        ]
    ]
);

try {
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo $e->getMessage();
}