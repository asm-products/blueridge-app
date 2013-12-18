<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

use Blueridge\Providers\Basecamp;
use Blueridge\Documents\User;
use Blueridge\Utilities\Doorman;
use Blueridge\Authentication\Adapter as AuthAdapter;
use Zend\Authentication\Result;


/**
 * Connect to Basecamp
 */
$app->get('/basecamp/connect/',function() use ($app,$blueridge){

    $settings = $blueridge['configs']['providers']['basecamp'];

    // if (empty($_SESSION['user'])) {
    $auth_request = "{$settings['auth_url']}?client_id={$settings['client_id']}&redirect_uri={$settings['redirect_uri']}&type=web_server";         
    $app->redirect($auth_request);

    // }else{
    //     $app->redirect('/app/todos'); 
    // }

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


    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneByEmail($me['email']);

    if(!empty($user)){
        $app->redirect('/sign-in/');
    }

    $activation = Doorman::getCode();

    $me['member_since'] = new \DateTime();
    $me['status']='new';
    $me['roles']='user';        
    $me['key']= $activation['key'];
    

    $user = new User;                     
    $user->setProperties($me);
    $blueridge['documentManager']->persist($user);
    $blueridge['documentManager']->flush();

    
    Resque::enqueue('subscription', 'Blueridge\Jobs\Push\CreateCustomer', ['user'=>$user->toArray(),'plan'=>'br-free','service'=>$blueridge['configs']['services']['subscriber']['stripe']]); 
    Resque::enqueue('mail', 'Blueridge\Jobs\Push\SignUpEmail', ['name'=>$user->name,'firstName'=>$user->firstName,'email'=>$user->email,'password'=>$activation['code'],'postman'=>$blueridge['configs']['services']['mail']['mandrill']]);

    $basecampDetails =  [
    'token'=>$token,
    'accounts'=>$authorization['accounts'],
    'identity'=>$authorization['identity'],
    ];

    $userQr->addProvider($user,'basecamp',$basecampDetails);

    $authAdapter = new AuthAdapter($userQr,$me['email'],$activation['code']);
    $result = $blueridge['authenticationService']->authenticate($authAdapter);

    switch ($result->getCode()) {

        case Result::SUCCESS:        
        if($user->status != 'active'){
            $app->redirect('/app/projects/');
        }
        $app->redirect('/app/todos/');
        break;

        default:
        $app->response()->status(403);
        $app->flash('errors', $result->getMessages());
        $app->redirect('/');
        break;
    }

});