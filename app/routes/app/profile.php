<?php
/**
 * Profile Routes
 */

use \BlueRidge\Entities\User;
use \BlueRidge\Entities\Todo;

$app->get('/app/profile/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user= new User($app);
    $user->fetchOne($id);

    $user = $user->toArray();
    $app->render("app/profile.html", ['user' =>$user ,'route'=>'profile']);    
});

$app->post('/app/profile/',$authenticate($app),function() use ($app){

});