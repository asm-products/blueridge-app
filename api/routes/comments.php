<?php
/**
 * Comments
 */
// PUT /api/todos/6556563/ - update
// POST - add new comment 
use Blueridge\Documents\Todo;
use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;


$app->get('/api/comments/', function () use ($app,$blueridge) {

    // validate for ajax calls
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

    $params = $app->request()->get();
    $collection=Array();        


    if(!empty($params['todoId']))
    {
        $todo = $todoQr->findOneByTodoId($params['todoId']); 
        if(!empty($todo)){
            $collection = array_slice($todo->source['comments'],0,3);        
        }       
    }

    if(!empty($params['userId']))
    {
        // set query builder
        $user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $params['userId']);
        $todos = $todoQr->fetchByUser($user); 
        foreach($todos as $todo){
            $collection[] = $todo->toArray();
        }         
    }

    if(empty($collection)){
        $app->response()->status(404);
    }else{
        $resource = json_encode($collection);
        echo $resource;  
    }
    $app->response->headers->set('Content-Type', 'application/json');

});