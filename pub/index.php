<?php
/**
 * BlueRidgeApp
 */
require '../vendor/autoload.php';

use \Slim\Slim;
use \Slim\Views;
use \Slim\Middleware\SessionCookie;

use \Blueridge\Middleware\Bootstrap;
use \Blueridge\Middleware\Db;
use \Blueridge\Middleware\Cache;
use \Blueridge\Middleware\Mail;
use \Blueridge\Middleware\View;
use \Blueridge\Middleware\Subscription;

defined('APP_PATH') || define('APP_PATH', realpath(dirname(__FILE__).'/../app'));
defined('API_PATH') || define('API_PATH', realpath(dirname(__FILE__).'/../api'));
defined('BIN_PATH') || define('BIN_PATH', realpath(dirname(__FILE__).'/../bin'));
defined('CACHE_DIR') || define('CACHE_DIR', realpath(dirname(__FILE__).'/../cache'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$app = new Slim();
$app->setName('blueridgeapp');
$app->add(new SessionCookie(['secret' => '4VtUZrv8@Y','name'=>'_blrdgapp','expires'=>'24 hours']));
$app->add(new Db());
$app->add(new Cache());
$app->add(new Mail());
$app->add(new View());
$app->add(new Bootstrap());

$authenticate = function ($app) {
    return function () use ($app) { 
        if(empty($_SESSION['live'])){            
            if(!empty($_SESSION['user'])){    
                $_SESSION['live']=time();
            }else{
                $app->flash('error', 'Connect to our Basecamp account ');
                $app->redirect('/');
            }
        }
    };
};

require APP_PATH."/routes/auth/connect.php";
require APP_PATH."/routes/auth/basecamp.php";
require APP_PATH."/routes/app/export.php";
require APP_PATH."/routes/app/projects.php";
require APP_PATH."/routes/app/todos.php";
require APP_PATH."/routes/app/profile.php";
require APP_PATH."/routes/app/cart.php";
require APP_PATH."/routes/app/sign-out.php";
require APP_PATH."/routes/site/home.php";
require APP_PATH."/routes/site.php";

$app->run();