<?php
/**
 * Connect Routes
 */

/**
 * Bacecamp Connect
 */
$app->get('/connect/basecamp',function() use ($app){
    
    if(isset($app->providers->$name)){
        $providerName  = "\\BlueRidge\\Providers\\{$app->providers->basecamp->handler}";
    }else{
        return ;
    }
    $provider = new $providerName($app);
    $app->redirect($provider->authUrl);
});

$app->response->headers->set('Content-Type', 'text/html');