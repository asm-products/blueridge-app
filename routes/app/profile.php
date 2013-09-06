<?php
/**
 * Profile Routes
 */

use \BlueRidge\Utilities\Teller;

$app->get('/app/profile/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id)->toArray();
    
    $payment = Teller::getPayment($app->config('services')['subscriber'],$user['subscription']);
    $app->render("app/profile.html", ['user' =>$user,'payment'=>$payment ,'route'=>'profile']);    
});