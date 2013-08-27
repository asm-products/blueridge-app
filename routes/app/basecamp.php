<?php
/**
 * Routes for Basecamp Auths
 */

use \BlueRidge\Entities\User;
use \BlueRidge\Utilities\Postman;
use \BlueRidge\Utilities\Doorman;
use \BlueRidge\Providers\BasecampApi;


$app->get('/app/basecamp/',function() use ($app){    
    $code = $app->request()->params('code');

    if(empty($code)){
        // respond with friendly message
    }

    if(isset($app->config('providers')['basecamp'])){
        $provider = new BasecampApi($app); 
        $properties= $provider->authorize($code);

        $user = new User($app,$properties);

        // check if user exists
        //$exists = $user->exists(['email'=>$properties['email']]);            
        //if(!empty($exists)){

            //return $this->refresh($properties);
        //}

        //$user = new User($app);
        
        var_dump($properties);
        exit();
        //$user->create($provider);

        $access = Doorman::Init();
        $user->key = $access['key'];
        $user->save();
        //$properties['key']=$access['key'];

        var_dump($user);
        exit();

        $_SESSION['user'] = $user->id;

        Postman::newUserMail($app,$user,$access);
        $app->redirect('/app/projects/');

    }else{
        // flash error
    }

});