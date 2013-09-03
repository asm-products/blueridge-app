<?php
/**
 *  Auth Basecamp
 */

use \BlueRidge\Providers\BasecampClient;
use \BlueRidge\Documents\User;
use \BlueRidge\Utilities\Postman;
use \BlueRidge\Utilities\Doorman;


$app->get('/auth/basecamp/',function() use ($app){
    $code = $app->request()->params('code');
    $settings = $app->config('providers')['basecamp'];
    $subscribe_settings = $app->config('services')['subscriber'];
    $noob = false;

    if(empty($code))
    {        
        if(!empty($settings)){
            $auth_request = "{$settings['auth_url']}?client_id={$settings['client_id']}&redirect_uri={$settings['redirect_uri']}&type=web_server";        
            $app->redirect($auth_request);
        }else{
            $app->render("common/error-500.html",['message'=>'Looks like we have a problem connecting you to Basecamp',500]);
        }
    }else
    {

        $basecampClient = new BasecampClient($settings);         
        $basecampClient->getToken($code)->getAuthorization();

        $access = Doorman::Init();
        $user = $app->dm->getRepository('\BlueRidge\Documents\User')->findOneByEmail($basecampClient->identity['email_address']);
        if(empty($user))
        {
            $user = new User;            
            $user->key = $access['key'];
            $user->profile = [
            'accounts'=>$basecampClient->getAccounts(),
            'projects'=>[]
            ];
            $me = $basecampClient->getMe();
            $user->setProperties($me);
            $user->initNewSubscriber($subscribe_settings);

            $noob = true;
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
        if($noob){
            Postman::newUserMail($app,$user,$access);    
        }
        
        $app->redirect('/app/projects/');

    }
});

$app->response->headers->set('Content-Type', 'text/html');