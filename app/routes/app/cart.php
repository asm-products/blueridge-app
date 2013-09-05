<?php
/**
 * Cart
 */

use \BlueRidge\Documents\User;

$app->post('/app/cart/update-payment/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id)->toArray();
    $app->render("app/cart.html", ['plan' => $plan,'user'=>$user,'route'=>'profile']);    
});

$app->post('/app/cart/update-subscription/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id)->toArray();
    $app->render("app/cart.html", ['plan' => $plan,'user'=>$user,'route'=>'profile']);    
});