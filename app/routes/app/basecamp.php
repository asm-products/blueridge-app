<?php
/**
 * Routes for Basecamp Auths
 */

use \BlueRidge\Entities\User;
use \BlueRidge\Utilities\Postman;
use \Zend\Session\Container;


$app->get('/app/basecamp/',function() use ($app){    
    $code = $app->request()->params('code');

    if(empty($code)){
        // respond with friendly message
    }
    

    if(isset($app->providers->basecamp)){
        $handler  = "\\BlueRidge\\Providers\\{$app->providers->basecamp->handler}";
    }else{
        return ;
    }
    $provider = new $handler($app); 
    $provider->authorize($code);

    $user = new User($app);
    $response= $user->create($provider);

    if($response['status'] == 500){
        $app->response()->status(500);
        //registration failed
        
    }else{
        $app->response()->status($response['status']);

        $session = new Container('blrdgapp');
        $session->userid = $user->id;
        
        //Postman::newUserMail($app,$response['resource'],$response['access']);

        $app->redirect('/app/projects/');

        
        
    }

});