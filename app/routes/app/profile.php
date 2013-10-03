<?php
/**
 * Profile Routes
 */

use \Blueridge\Utilities\Teller;

$app->get('/app/profile/',$authenticate($app), function () use ($app) {
    $id = base64_decode($_SESSION['user']);
    $subscriber= $app->config('services')['subscriber'];    
    $user = $app->dm->find('\Blueridge\Documents\User', $id);

    if (empty($user)){
        unset($_SESSION['live']); 
        unset($_SESSION['user']);
        $app->redirect('/');  
    }
    
    $app->render("app/profile.html", ['user' =>$user->toArray(),'subscriber'=>$subscriber ,'route'=>'profile']);    
});