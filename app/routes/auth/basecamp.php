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



$app->get('/auth/basecamp/',function() use ($app){

    $code = $app->request()->params('code');
    $settings = $app->config('providers')['basecamp'];
    $newbie = false;      

    if(empty($code))
    {       
        $app->redirect('/connect/basecamp');         
    }else
    {

        $_SESSION['live']=time();

        $config = BasecampClientHelper::getConfig($app);                   
        $token = BasecampClientHelper::getToken($config, $code);
        $config= array_merge($config,$token);
        $authorization = BasecampClientHelper::getAuthorization($config);
        $me = BasecampClientHelper::getMe($config,$authorization);

        $access = Doorman::getAccess();
        $qr= $app->dm->getRepository('\Blueridge\Documents\User');
        $user = $qr->findOneByEmail($me['email']);

        if(empty($user))
        {
            $user = new User;            
            $user->key = $access['key'];
            $user->profile = [
            'accounts'=>$authorization['accounts'],
            'projects'=>[]
            ];

            $user->setProperties($me);
            $user->subscription = Teller::addCustomer($app->config('services')['subscriber'],$me);                        
            $newbie = true;
        }

        $user->providers = ['basecamp'=>[
        'token'=>$token,
        'accounts'=>$authorization['accounts'],
        'identity'=>$authorization['identity'],
        ]];
        $user->projects= BasecampClientHelper::getProjects($config,$authorization);
        $app->dm->persist($user);
        $app->dm->flush();


        $_SESSION['user'] = base64_encode($user->id);        
        $app->flash('newbie', $newbie);
        if($newbie){
            $app->redirect('/app/projects/');            
        }
        $app->redirect('/app/todos/');
    }
});

$app->response->headers->set('Content-Type', 'text/html');