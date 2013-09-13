<?php
/**
 * Todo Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Providers\Basecamp\BasecampClient;

$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\Blueridge\Documents\User', $id);

    $basecampClient = BasecampClient::factory($app)->setAuth($user->providers['basecamp']);
    $todos = $basecampClient->getTodos($user);

    $app->render("app/todos.html", ['todos' => $todos,'route'=>'todos']);    
});

