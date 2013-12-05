<?php
/**
 * BlueRidgeApp
 */

// Set Constants
defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(dirname(__FILE__).'/../'));
defined('APP_PATH') || define('APP_PATH', APPLICATION_ROOT.'/app');
defined('API_PATH') || define('API_PATH', APPLICATION_ROOT.'/api');
defined('BIN_PATH') || define('BIN_PATH', APPLICATION_ROOT.'/bin');
defined('CACHE_DIR') || define('CACHE_DIR', APPLICATION_ROOT.'/cache');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require APPLICATION_ROOT.'/vendor/autoload.php';

use \Slim\Slim;
use \Slim\Views;
use \Slim\Middleware\SessionCookie;
use \Blueridge\Blueridge;
use \Blueridge\Middleware\View;


$app = new Slim();
$blueridge = new Blueridge();

$app->setName('blueridgeapp');
$app->add(new SessionCookie(['secret' => '4VtUZrv8@Y','name'=>'_blrdgapp','expires'=>'24 hours']));
$app->add(new View());


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

require APP_PATH."/init.php";
require API_PATH."/init.php";



$app->run();