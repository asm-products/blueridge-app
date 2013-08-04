<?php
/**
 * BlueRidge App Api
 */


require '../vendor/autoload.php';

use \Slim\Slim;
use \Slim\Extras\Middleware\CsrfGuard;
use \BlueRidge\Init;


$app = new Slim();
$app->setName('blueridgeapi');
$app->add(new Init());

require "../api/routes/providers.php";
require "../api/routes/services.php";
require "../api/routes/users.php";
require "../api/routes/auth.php";
require "../api/routes/subscriptions.php";

$response = $app->response();
$response['Content-Type'] = 'application/json';
$app->run();