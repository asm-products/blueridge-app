<?php
/**
 * Todo Routes
 */

use \BlueRidge\Documents\User;
use \BlueRidge\Entities\Todo;

$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id);


    $todo = new Todo($app);
    $todos = $todo->fetchByUser($user);

    $app->render("app/todos.html", ['todos' => $todos,'route'=>'todos']);    
});

