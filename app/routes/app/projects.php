<?php
/**
 * Get User
 */

use \Blueridge\Documents\User;

$app->get('/app/projects/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $noob = (isset($_SESSION['noob']))?true:false;
    $user = $app->dm->find('\Blueridge\Documents\User', $id);
    $projects = $user->projects;
    $app->render("app/projects.html", ['projects' => $projects,'route'=>'projects','noob'=>$noob,'plan'=>$user->toArray()['subscription']['plan']]);    
});

$app->post('/app/projects/',$authenticate($app),function() use ($app){

    $params = $app->request->post('selected');
    $id = $_SESSION['user'];

    $user = $app->dm->find('\Blueridge\Documents\User', $id);
    $user->updateProfile('projects',$params);
    $user->url = "/users/{$user->id}";
    $app->dm->flush($user);
    unset($_SESSION['noob']);    
    $app->redirect('/app/todos/');
});