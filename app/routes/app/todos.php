<?php
/**
 * Todo Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Providers\Basecamp;


$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = base64_decode($_SESSION['user']);
    $user = $app->dm->find('\Blueridge\Documents\User', $id);

    if (empty($user)){
        unset($_SESSION['live']); 
        unset($_SESSION['user']);
        $app->redirect('/');  
    }
    $basecampClient = new Basecamp($app);
    $todos = $basecampClient->getTodos($user);

    $app->render("app/todos.html", ['user' =>$user->toArray(),'todos' => $todos,'route'=>'todos']);
});

