<?php
/**
 * Blueridge 
 * 
 * Site routes
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

$app->get('/',function() use ($app,$blueridge){

    if($blueridge['authenticationService']->hasIdentity()){

        $userQr = $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
        $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());
        $app->setCookie('_blrg_connect', $_SERVER['REQUEST_TIME'], '14 days');

        if($user->status != 'active'){
            $app->redirect('/app/projects/');
        }

        $app->redirect('/app/todos/');
    }
    
    $view = ['mode'=>$app->mode,'connected'=>false];
    
    if($app->getCookie('_blrg_connect')){
        $view['connected']=true;
    }

    $app->render('site/home.html',$view);
    $app->response->headers->set('Content-Type', 'text/html');
});