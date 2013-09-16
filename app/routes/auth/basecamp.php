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

    if(empty($code))
    {        
        if(!empty($settings))
        {
            $auth_request = "{$settings['auth_url']}?client_id={$settings['client_id']}&redirect_uri={$settings['redirect_uri']}&type=web_server";        
            $app->redirect($auth_request);
        }else
        {
            $app->render("common/error-500.html",['message'=>'Looks like we have a problem connecting you to Basecamp',500]);
        }
    }else
    {
        
        $config = BasecampClientHelper::getConfig($app);                   
        $token = BasecampClientHelper::getToken($config, $code);
        $config= array_merge($config,$token);
        $authorization = BasecampClientHelper::getAuthorization($config);
        $me = BasecampClientHelper::getMe($config,$authorization);

        $access = Doorman::Init();
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
            $_SESSION['noob'] = true;
        }

        $user->providers = ['basecamp'=>[
        'token'=>$token,
        'accounts'=>$authorization['accounts'],
        'identity'=>$authorization['identity'],
        ]];
        $user->projects= BasecampClientHelper::getProjects($config,$authorization);
        $app->dm->persist($user);
        $app->dm->flush();

        $_SESSION['user'] = $user->id;
        if(isset($_SESSION['noob'])){
            Postman::newUserMail($app,$user,$access);    
        }
        $app->redirect('/app/projects/');
    }
});

$app->response->headers->set('Content-Type', 'text/html');