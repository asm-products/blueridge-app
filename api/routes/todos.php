<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

use Blueridge\Documents\Todo;
use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;

/**
 * Todo
 */
// PUT /api/todos/6556563/ - update
// POST - add new comment 

/**
 * Get all todos for the user
 * @param String u User Id
 */
$app->get('/api/todos/',function () use ($app,$blueridge) {

    /**
     * @todo validate for xhr requests 
     */
    
    $userid= $app->request()->get('userid');
    if(empty($userid)){
        $app->response()->status(404);
        return;
    }

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');     
    $user = $userQr->findOneById($userid);
    if(empty($user)){
        $app->response()->status(404);
        return;
    }

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $projects = $user->profile['projects'];
    if(empty($projects)){
        // Add message to let them select project
        $app->response()->status(404);
        return;
    }

    $userTodos = Array();

    foreach ($projects as $selectedProject) {

        $projectTodos = $todoQr->fetchByProject($user,$selectedProject)->toArray();

        if(empty($projectTodos)){
            $basecampClient = new Basecamp($blueridge);
            $todoItems = $basecampClient->getTodos($user,[$selectedProject]);
            
            foreach($todoItems as $item){

                $item['todoId']=$item['rel']['project']['account']['product'].'_'.$item['id'];
                unset($item['id']);

                $basecampClient = new Basecamp($blueridge);
                $item['source'] = $basecampClient->getTodo($user,$item['url']);

                $todo = $todoQr->findOneByTodoId($item['todoId']);
                if(empty($todo)){
                    $todo = new Todo();
                }
                $item  = $todo->polish($item);
                $todo->setProperties($item);        
                $blueridge['documentManager']->persist($todo);
                $blueridge['documentManager']->flush();
            }

            $projectTodos = $todoQr->fetchByProject($user,$selectedProject)->toArray();
        }

        $userTodos = array_merge($userTodos,$projectTodos);
    }

    $todos = array_map(function($todo){
        return $todo->toArray();
    },array_values($userTodos));
    
    
    $collection = ['count'=>count($todos),'projects'=>count($projects),'todos'=>$todos];
    echo json_encode($collection);
    $app->response->headers->set('Content-Type', 'application/json');

});



/**
 * $id Todo Id
 * @var String
 */
$app->post('/api/todos/:id/',function ($id) use ($app,$blueridge) {

    /**
     * @todo check fot xhr
     */
    $userid = $app->request()->params('user');
    $payload = $app->request()->params('payload');

    
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');     
    $user = $userQr->findOneById($userid);
    if(empty($user)){
        $app->response()->status(404);
        return;
    }

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $todo = $todoQr->findOneByTodoId($id);

    $basecampClient = new Basecamp($blueridge);
    $result = $basecampClient->updateTodo($user,$todo,$payload);

    if($result==200){
        $blueridge['documentManager']->remove($todo);                 
        $blueridge['documentManager']->flush(); 
    }
    $app->response()->status($result);    

});