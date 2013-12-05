<?php
/**
 *  Auth Basecamp
 */

use Blueridge\Providers\Basecamp;
use Blueridge\Documents\User;
use Blueridge\Utilities\Doorman;
use Blueridge\Utilities\Teller;



$app->get('/auth/basecamp/',function() use ($app,$blueridge){


    $code = $app->request()->params('code');
    $settings = $blueridge['configs']['providers']['basecamp'];


    $newbie = false;      

    if(empty($code))
    {       
        $app->redirect('/connect/basecamp');         
    }else
    {

        $_SESSION['live']=time();

        $basecampClient = new Basecamp($blueridge);
        $token = $basecampClient->getToken($code);
        $authorization = $basecampClient->getAuthorization($token);
        $me = $basecampClient->getMe($authorization);

        $me['key']= Doorman::getAccess()['key'];
        $me['profile'] = [
        'accounts'=>$authorization['accounts'],
        'projects'=>[]
        ];



        // check for user
        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $user = $userQr->findOneByEmail($me['email']);



        if(empty($user))
        {
            $me['member_since'] = new \DateTime();
            $user = new User;                     
            $user->setProperties($me);
            $blueridge['documentManager']->persist($user);
            $blueridge['documentManager']->flush();

            $user = $userQr->findOneByEmail($me['email']);

            // add to que
            $user->subscription = Teller::addCustomer($blueridge['configs']['services']['subscriber'],$me);
            $newbie = true;
        }

        $user->providers = ['basecamp'=>[
        'token'=>$token,
        'accounts'=>$authorization['accounts'],
        'identity'=>$authorization['identity'],
        ]];

        $blueridge['documentManager']->persist($user);
        $blueridge['documentManager']->flush();


        Resque::enqueue('default', 'Blueridge\Jobs\Pull\Projects', ['userId'=>$user->id]);
        Resque::enqueue('default', 'Blueridge\Jobs\Pull\Todos', ['userId'=>$user->id]);

        $_SESSION['user'] = base64_encode($user->id);        
        $app->flash('newbie', $newbie);
        if($newbie){
            $app->redirect('/app/projects/');            
        }

        $app->redirect('/app/todos/');
    }
});

$app->response->headers->set('Content-Type', 'text/html');