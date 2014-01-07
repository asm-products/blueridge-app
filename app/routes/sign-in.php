<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Authentication\Adapter as AuthAdapter;
use Zend\Authentication\Result;

/**
 * Show sign in form
 */
$app->get("/sign-in/", function () use ($app,$blueridge) {

    if($blueridge['authenticationService']->hasIdentity()){
        $app->redirect('/app/todos/');
    }

    if($app->getCookie('_blrgapp')){
        
        // $view['connected']=true;
    }

    $view = [
    'route' => 'sign-in',
    'mode'=>$app->mode
    ];
    $app->render("site/sign-in.html", $view);
    $app->response->headers->set('Content-Type', 'text/html');
});


$app->post("/sign-in/", function () use ($app,$blueridge) {

    $email = filter_var($app->request()->post('email'), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $app->response()->status(403);
        $app->flash('errors', ['Invalid email address']);
        $app->redirect("/sign-in/");
    }

    $password = $app->request()->post('password');

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $authAdapter = new AuthAdapter($userQr,$email,$password);
    $result = $blueridge['authenticationService']->authenticate($authAdapter);

    switch ($result->getCode()) {        
        case Result::SUCCESS:        
        $user = $userQr->findOneByEmail($result->getIdentity());
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