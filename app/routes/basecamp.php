<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Providers\Basecamp;
use Blueridge\Documents\User;
use Blueridge\Utilities\Doorman;
use Blueridge\Utilities\Teller;
use Blueridge\Authentication\ProviderAdapter;
use Zend\Authentication\Result;


/**
 * Connect to Basecamp
 */
$app->get('/basecamp/connect/',function() use ($app,$blueridge){

    $settings = $blueridge['configs']['providers']['basecamp'];
    if ($session= $blueridge['authenticationService']->hasIdentity()===false) {
        $auth_request = "{$settings['auth_url']}?client_id={$settings['client_id']}&redirect_uri={$settings['redirect_uri']}&type=web_server";         
        $app->redirect($auth_request);
    }
    $app->redirect("/app/todos/");
});

/**
 * Authorize the Basecamp connection
 */
$app->get('/basecamp/auth/',function() use ($app,$blueridge){

    $code = $app->request()->params('code');
    $settings = $blueridge['configs']['providers']['basecamp'];     

    if(empty($code))
    {
        $app->redirect('/error/basecamp-connect/');        
    }


    $basecampClient = new Basecamp($blueridge);
    $token = $basecampClient->getToken($code);
    $authorization = $basecampClient->getAuthorization($token);
    $me = $basecampClient->getMe($authorization);
    
    $me['profile'] = [
    'accounts'=>$authorization['accounts'],
    'projects'=>[]
    ];
    $me['identifier'] = Doorman::getToken();

    $basecampDetails =  [
    'token'=>$token,
    'accounts'=>$authorization['accounts'],
    'identity'=>$authorization['identity'],
    ];


    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneByEmail($me['email']);

    if(empty($user)){

        $activation = Doorman::getCode();
        $me['member_since'] = new \DateTime();
        $me['status']='new';
        $me['roles']='user';        
        $me['key']= $activation['key'];        

        $user = new User;                     
        $user->setProperties($me);
        $blueridge['documentManager']->persist($user);
        $blueridge['documentManager']->flush();   

        $subscription=Teller::addCustomer($blueridge['configs']['services']['subscriber'],$user->toArray());     
        $userQr->setSubscription($user,$subscription);

        Resque::enqueue('mail', 'Blueridge\Jobs\Push\SignUpEmail', ['email'=>$user->email,'postman'=>$blueridge['configs']['services']['mail']['mandrill']]);
        
    }

    $userQr->setProvider($user,'basecamp',$basecampDetails);

    $providerAdapter = new ProviderAdapter($blueridge['documentManager'],$user->identifier);
    $result = $blueridge['authenticationService']->authenticate($providerAdapter);


    switch ($result->getCode()) {        
        case Result::SUCCESS:         
        $app->setCookie('_blrdg_connect', $_SERVER['REQUEST_TIME'], '14 days');
        if($user->status != 'active'){
            $app->redirect('/app/projects/');
        }
        $app->redirect('/app/todos/');
        break;                
        default:
        $app->response()->status(403);
        $app->flash('errors', $result->getMessages());
        $app->redirect('/sign-in/');
        break;
    }
});