<?php
/**
 * Profile Routes
 */

use \Blueridge\Utilities\Teller;

$app->get('/app/profile/',$authenticate($app), function () use ($app) {
    $id = $_SESSION['user'];
    $subscriber= $app->config('services')['subscriber'];
    
    $user = $app->dm->find('\Blueridge\Documents\User', $id)->toArray();    
    $app->render("app/profile.html", ['user' =>$user,'subscriber'=>$subscriber ,'route'=>'profile']);    
});