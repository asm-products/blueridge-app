<?php
/**
 * Profile Routes
 */

use \BlueRidge\Utilities\Teller;

$app->get('/app/profile/',$authenticate($app), function () use ($app) {
    $id = $_SESSION['user'];
    $subscriber= $app->config('services')['subscriber'];
    
    $user = $app->dm->find('\BlueRidge\Documents\User', $id)->toArray();    
    $app->render("app/profile.html", ['user' =>$user,'subscriber'=>$subscriber ,'route'=>'profile']);    
});