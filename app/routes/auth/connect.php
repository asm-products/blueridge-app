<?php
/**
 *  Auth Basecamp
 */

use \Blueridge\Providers\Basecamp\BasecampClient;
use \Blueridge\Providers\Basecamp\BasecampClientHelper;
use \Blueridge\Documents\User;
use \Blueridge\Utilities\Postman;
use \Blueridge\Utilities\Doorman;
use \Blueridge\Utilities\Teller;



$app->get('/connect/basecamp/',function() use ($app){

    $settings = $app->config('providers')['basecamp']; 

    if (empty($_SESSION['user'])) {
        $auth_request = "{$settings['auth_url']}?client_id={$settings['client_id']}&redirect_uri={$settings['redirect_uri']}&type=web_server";        
        $app->redirect($auth_request);

    }else{
        $app->redirect('/app/todos'); 
    }



});

$app->response->headers->set('Content-Type', 'text/html');