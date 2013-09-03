<?php
/**
 * Get User
 */

use \BlueRidge\Documents\User;

$app->get('/app/projects/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id);
    $projects = $user->projects; 
    $app->render("app/projects.html", ['projects' => $projects,'route'=>'projects']);    
});

$app->post('/app/projects/',$authenticate($app),function() use ($app){

    $params = $app->request->post('selected');
    $id = $_SESSION['user'];

    $user = $app->dm->find('\BlueRidge\Documents\User', $id);
    $user->updateProfile('projects',$params);
    $user->url = "/users/{$user->id}";
    $app->dm->flush($user);
    
    $app->redirect('/app/todos/');
});