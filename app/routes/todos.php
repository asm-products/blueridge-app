<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Zend\Session\Container;

/**
 * Todo Routes
 * @param type '/app/todos/' 
 * @param type function () use ($app 
 * @param type $blueridge 
 * @return type
 */
$app->get('/app/todos/',function () use ($app,$blueridge) {
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());

    if(empty($user)){
        $app->redirect('/sign-out/');
    }
    $view = [
    'user' =>$user->toArray(),
    'route'=>'todos',
    'mode'=>$app->mode
    ];
    $app->render("app/todos.html", $view);
});

