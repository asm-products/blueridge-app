<?php
/**
 * Cart
 */

use \BlueRidge\Documents\User;

$app->get('/app/cart/:plan/',$authenticate($app), function ($plan) use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id)->toArray();
    $app->render("app/cart.html", ['plan' => $plan,'user'=>$user,'route'=>'profile']);    
});