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
        $access = Doorman::Init();

        $user = new User($app,$properties);
        $exists = $user->exists(['email'=>$properties['email']]);

        if(empty($exists)){

            $user->id = (string) new \MongoId();            
            $user->profile = [
            'accounts'=>$provider->getAccounts(),
            'projects'=>[]
            ];
            $user->url = "/users/{$user->id}";

            
            $user->key = $access['key'];
            $user->setSubscription();
        }
        $user->providers= [$provider->name =>$provider->getProperties()];       
        $user->projects= $provider->getProjects();
        $user->save();
        
        $_SESSION['user'] = $user->id;

        Postman::newUserMail($app,$user,$access);
        $app->redirect('/app/projects/');

    }else{
        // flash error
    }

});