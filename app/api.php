<?php
/**
 * BlueRidge App Api
 */

require '../vendor/autoload.php';

use \Slim\Slim;
use \BlueRidge\Init;

$app = new Slim();
$app->add(new Init());
require "../api/routes/providers.php";
require "../api/routes/users.php";

$response = $app->response();
$response['Content-Type'] = 'application/json';
$app->run();