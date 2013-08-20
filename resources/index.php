<?php
/**
 * BlueRidgeApp
 */
require '../vendor/autoload.php';
use \Slim\Slim;
use \Slim\Views;

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../app'));
defined('CACHE_DIR') || define('CACHE_DIR', realpath(dirname(__FILE__).'/../cache'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$app = new Slim([
    'view' => new Views\Twig(),
    'templates.path' => APPLICATION_PATH.'/resources/views'
    ]);

$view = $app->view();
$view->parserOptions = ['debug' => true,'cache' => CACHE_DIR];
$view->parserExtensions = [ new \Slim\Views\TwigExtension()];


require APPLICATION_PATH."/routes/site/home.php";

/*require "../api/routes/services.php";
require "../api/routes/users.php";
require "../api/routes/auth.php";
require "../api/routes/subscriptions.php";
*/

$app->run();