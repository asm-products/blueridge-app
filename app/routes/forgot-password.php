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
$app->get("/forgot-password/", function () use ($app,$blueridge) {
    if($blueridge['authenticationService']->hasIdentity()){
        $app->redirect('/app/todos/');
    }
    $view = [
    'route' => 'forgot-password',
    'mode'=>$app->mode
    ];
    $app->render("site/forgot-password.html", $view);
    $app->response->headers->set('Content-Type', 'text/html');

});


$app->post("/forgot-password/", function () use ($app,$blueridge) {

    $email = filter_var($app->request()->post('email'), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $app->response()->status(403);
        $app->flash('errors', ['Invalid email address']);
        $app->redirect("/forgot-password/");
    }


    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneByEmail($email);  

    if(empty($user)){
        $app->redirect('/basecamp/connect/');
    }
    
    $activation = Doorman::getCode();
    $userQr->updateActivation($user,$activation['key']);

    Resque::enqueue('mail', 'Blueridge\Jobs\Push\ForgotPasswordEmail', ['user'=>$user->toArray(),'code'=>base64_encode($activation['code']),'postman'=>$blueridge['configs']['services']['mail']['mandrill']]);

    var_dump($user);
    exit();



});