<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

include "/var/www/env.php";

// Set Constants
defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(dirname(__FILE__).'/../'));
defined('APP_PATH') || define('APP_PATH', APPLICATION_ROOT.'/app');
defined('API_PATH') || define('API_PATH', APPLICATION_ROOT.'/api');
defined('BIN_PATH') || define('BIN_PATH', APPLICATION_ROOT.'/bin');
defined('CACHE_DIR') || define('CACHE_DIR', APPLICATION_ROOT.'/cache');

require APPLICATION_ROOT.'/vendor/autoload.php';

use \Slim\Slim;
use \Slim\Views;
use \Slim\Middleware\SessionCookie;
use \Blueridge\Application;
use \Blueridge\Middleware\Authentication;
use \Blueridge\Middleware\View;



$blueridge = new Application();
$app = new Slim($blueridge['configs']['app']);

$app->setName('blueridgeapp');
$app->add(new View());
$app->add(new Authentication($blueridge));

require APP_PATH."/init.php";
require API_PATH."/init.php";
$app->run();