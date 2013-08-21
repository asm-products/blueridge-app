<?php
/**
 * BlueRidgeApp
 */
require '../vendor/autoload.php';
use \Slim\Slim;
use \Slim\Views;
use \BlueRidge\Init;

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../app'));
defined('CACHE_DIR') || define('CACHE_DIR', realpath(dirname(__FILE__).'/../cache'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$app = new Slim([
    'view' => new Views\Twig(),
    'templates.path' => APPLICATION_PATH.'/resources/views'
    ]);
$app->setName('blueridgeapp');
$app->add(new Init());


$view = $app->view();
$view->parserOptions = ['debug' => true,'cache' => CACHE_DIR.'/front'];
$view->parserExtensions = [ new \Slim\Views\TwigExtension()];


/**
 * Routes
 * Note: Always load individual routes before generic ones 
 */

// Site
require APPLICATION_PATH."/routes/site/connect.php";
require APPLICATION_PATH."/routes/site.php";

// App
require APPLICATION_PATH."/routes/app/basecamp.php";
require APPLICATION_PATH."/routes/app/export.php";
require APPLICATION_PATH."/routes/app.php";

$app->run();