<?php
/**
 * Todo Routes
 */

use \BlueRidge\Entities\User;

$app->get('/app/todos/',$authenticate($app), function () use ($app) {

    $id = $_SESSION['user'];
    $user= new User($app);
    $user->fetchOneById($id)->fetchSegment('todos');
    $app->render("app/todos.html", ['todos' => $user->projects,'route'=>'todos']);    
});

