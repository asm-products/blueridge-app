<?php
/**
 * Todo Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Providers\Basecamp\BasecampClientHelper;


$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\Blueridge\Documents\User', $id);

    $todos = BasecampClientHelper::getTodos($app,$user);
    $app->render("app/todos.html", ['todos' => $todos,'route'=>'todos']);
});

