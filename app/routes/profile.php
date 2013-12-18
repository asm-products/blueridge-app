<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 * @since 0.1.0
 */

$app->get('/app/profile/',function () use ($app,$blueridge) {

	$userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());

    if(empty($user)){
        $app->redirect('/');
    }
    
    $subscriber= $blueridge['configs']['services']['subscriber'];            
    $app->render("app/profile.html", ['user' =>$user->toArray(),'subscriber'=>$subscriber ,'route'=>'profile']);    
});