<?php
/**
 * Todo Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Providers\Basecamp\BasecampClientHelper;


$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = base64_decode($_SESSION['user']);
    $todouser = $app->dm->find('\Blueridge\Documents\User', $id);
    $todos = BasecampClientHelper::getTodos($app,$todouser);
    $user = $app->dm->find('\Blueridge\Documents\User', $id)->toArray();
    $app->render("app/todos.html", ['user' =>$user,'todos' => $todos,'route'=>'todos']);
});

