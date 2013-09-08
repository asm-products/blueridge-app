<?php
/**
 * Todo Routes
 */

use \BlueRidge\Documents\User;
use \BlueRidge\Providers\Basecamp\BasecampClient;

$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id);

    $basecampClient = BasecampClient::factory($app)->setAuth($user);
    $todos = $basecampClient->getTodos($user);

    $app->render("app/todos.html", ['todos' => $todos,'route'=>'todos']);    
});

