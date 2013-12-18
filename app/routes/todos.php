<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Zend\Session\Container;

/**
 * Todo Routes
 * @param type '/app/todos/' 
 * @param type function () use ($app 
 * @param type $blueridge 
 * @return type
 */
$app->get('/app/todos/',function () use ($app,$blueridge) {

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());

    if(empty($user)){
        $app->redirect('/sign-out/');
    }

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $projects = $user->profile['projects'];

    Resque::enqueue('default', 'Blueridge\Jobs\Utils\CleanUpTodos', ['userId'=>$user->id,'projects'=>$projects]);
    Resque::enqueue('default', 'Blueridge\Jobs\Pull\Todos', ['userId'=>$user->id,'projects'=>$projects]); 

    sleep(1);   

    $collection = $todoQr->fetchByUser($user);
    $todos = Array();
    foreach ($collection as $todo ) {
        $todos[]=$todo->toArray();
    }
    $app->render("app/todos.html", ['user' =>$user->toArray(),'todos' => $todos,'route'=>'todos']);
});

