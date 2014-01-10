<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 * @since 0.1.0
 */

$app->get('/app/profile/',function () use ($app,$blueridge) {

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneByIdentifier($blueridge['authenticationService']->getIdentity());
    
    $view = [
    'user' =>$user->toArray(),
    'subscriber'=>$blueridge['configs']['services']['subscriber'],
    'route'=>'profile',
    'mode'=>$app->mode
    ];
    $app->render("app/profile.html", $view);    
});