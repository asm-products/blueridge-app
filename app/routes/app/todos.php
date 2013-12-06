<?php
/**
 * Todo Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Documents\Todo;


$app->get('/app/todos/',$authenticate($app), function () use ($app,$blueridge) {

    $userId = base64_decode($_SESSION['user']);
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

    $user= $userQr->findOneById($userId);



    if (empty($user)){
        unset($_SESSION['live']); 
        unset($_SESSION['user']);
        $app->redirect('/');  
    }    

    $collection = $todoQr->fetchByUser($user);
    $todos = Array();
    foreach ($collection as $todo ) {
        $todos[]=$todo->toArray();
    }
    $app->render("app/todos.html", ['user' =>$user->toArray(),'todos' => $todos,'route'=>'todos']);
});

