<?php
/**
 * Connect Routes
 */

/**
 * Bacecamp Connect
 */
use \BlueRidge\Providers\BasecampApi;

$app->get('/connect/basecamp/',function() use ($app){

    if(isset($app->config('providers')['basecamp'])){
        
        $provider = new BasecampApi($app);
        $app->redirect($provider->authUrl);


    }else{
        return ;
    }
    
});

$app->response->headers->set('Content-Type', 'text/html');