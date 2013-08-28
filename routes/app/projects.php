<?php
/**
 * Get User
 */

use \BlueRidge\Entities\User;

$app->get('/app/projects/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user= new User($app);
    $projects = $user->fetchOne($id)->projects;
    $app->render("app/projects.html", ['projects' => $projects,'route'=>'projects']);    
});

$app->post('/app/projects/',$authenticate($app),function() use ($app){

    $params = $app->request->post('selected');
    $id = $_SESSION['user'];
    $user= new User($app);
    $user->fetchOne($id);
    $user->updateProfile('projects',$params);
    $user->save();
    $app->redirect('/app/todos/');
});