<?php
/**
 * BlueRidgeApp
 */
require '../vendor/autoload.php';

use \Slim\Slim;
use \Slim\Views;
use \Slim\Middleware\SessionCookie;

use \BlueRidge\Middleware\Bootstrap;
use \BlueRidge\Middleware\Db;
use \BlueRidge\Middleware\Mail;
use \BlueRidge\Middleware\View;
use \BlueRidge\Middleware\Subscription;

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../app'));
defined('CACHE_DIR') || define('CACHE_DIR', realpath(dirname(__FILE__).'/../cache'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$app = new Slim();
$app->setName('blueridgeapp');
$app->add(new SessionCookie(['secret' => '4VtUZrv8@Y','name'=>'_blrdgapp','expires'=>'24 hours']));
$app->add(new Db());
$app->add(new Mail());
$app->add(new View());
$app->add(new Bootstrap());

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/sign-in/');
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

require APPLICATION_PATH."/routes/auth/basecamp.php";

require APPLICATION_PATH."/routes/site/sign-in.php";
require APPLICATION_PATH."/routes/site/forgot-password.php";
require APPLICATION_PATH."/routes/site/home.php";

require APPLICATION_PATH."/routes/app/export.php";
require APPLICATION_PATH."/routes/app/projects.php";
require APPLICATION_PATH."/routes/app/todos.php";
require APPLICATION_PATH."/routes/app/profile.php";
require APPLICATION_PATH."/routes/app/cart.php";
require APPLICATION_PATH."/routes/app/sign-out.php";
//require APPLICATION_PATH."/routes/app.php";

$app->run();