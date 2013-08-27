<?php
/**
 * Get User
 */

use \BlueRidge\Entities\User;

$app->get('/app/projects/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user= new User($app);
    $user->fetchOneById($id);
    var_dump($user);
    exit();

    //->fetchSegment('projects');
    $app->render("app/projects.html", ['projects' => $user->projects,'route'=>'projects']);    
});

$app->post('/app/projects',$authenticate($app),function() use ($app){
    error_log('form post');

});