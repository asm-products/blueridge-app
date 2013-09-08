<?php
/**
 *  Auth Basecamp
 */

use \BlueRidge\Providers\Basecamp\BasecampClient;
use \BlueRidge\Documents\User;
use \BlueRidge\Utilities\Postman;
use \BlueRidge\Utilities\Doorman;
use \BlueRidge\Utilities\Teller;


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

        $basecampClient = BasecampClient::factory($app);
        $basecampClient->getToken($code)->getAuthorization();
        $me = $basecampClient->getMe();

        $access = Doorman::Init();
        $qr= $app->dm->getRepository('\BlueRidge\Documents\User');
        $user = $qr->findOneByEmail($me['email']);
        if(empty($user))
        {
            $user = new User;            
            $user->key = $access['key'];
            $user->profile = [
            'accounts'=>$basecampClient->accounts,
            'projects'=>[]
            ];
            
            $user->setProperties($me);
            $user->subscription = Teller::addCustomer($app->config('services')['subscriber'],$me);                        
            $_SESSION['noob'] = true;
        }

        $user->providers = ['basecamp'=>[
        'token'=>$basecampClient->token,
        'accounts'=>$basecampClient->accounts,
        'identity'=>$basecampClient->identity,
        ]];
        $user->projects= $basecampClient->getProjects();
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