<?php
/**
 * BlueRidgeApp
 */
require '../vendor/autoload.php';

use \Slim\Slim;
use \Slim\Views;
use \Slim\Middleware\SessionCookie;
use \BlueRidge\Init\Configs;
use \BlueRidge\Init\Db;
use \BlueRidge\Init\Mailbox;

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../app'));
defined('CACHE_DIR') || define('CACHE_DIR', realpath(dirname(__FILE__).'/../cache'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$app = new Slim();
$app->setName('blueridgeapp');
$app->add(new SessionCookie(array('secret' => '4VtUZrv8@Y')));
$app->add(new Db());
$app->add(new Mailbox());
$app->add(new Configs());

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/signin');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) { 
    $user = null;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }
    $app->view()->setData('user', $user);
});



/**
 * Routes
 * Note: Always load individual routes before generic ones 
 */

// Site
require APPLICATION_PATH."/routes/site/signin.php";
require APPLICATION_PATH."/routes/site/connect.php";
require APPLICATION_PATH."/routes/site.php";

// App
require APPLICATION_PATH."/routes/app/basecamp.php";
require APPLICATION_PATH."/routes/app/export.php";
require APPLICATION_PATH."/routes/app/projects.php";
require APPLICATION_PATH."/routes/app/todos.php";
require APPLICATION_PATH."/routes/app/profile.php";
require APPLICATION_PATH."/routes/app.php";

$app->run();