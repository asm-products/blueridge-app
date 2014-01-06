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
    
    $userId= $app->request()->get('u');
    if(empty($userId)){
        $app->response()->status(404);
        return;
    }

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');     
    $user = $userQr->findOneById($userId);
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
$app->get('/api/todos/(:id/)',function ($id = null) use ($app,$blueridge) {

    // validate for ajax calls
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

    $params = $app->request()->get();
    $collection=Array();        
    if(!empty($id))
    {
        $collection = $todoQr->findOneById($id);
        if(!empty($collection)){
            $collection->toArray();    
        }
    }

    if(!empty($params['todoId']))
    {
        $todo = $todoQr->findOneByTodoId($params['todoId']); 
        
        if(!empty($todo)){
            $collection= $todo->toArray();    
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

        $response = ['todos'=>$collection];
        $resource = json_encode($response);
        echo $resource;  
    }
    $app->response->headers->set('Content-Type', 'application/json');

});

/**
 * $id Todo Id
 * @var String
 */
$app->post('/api/todos/:id/',function ($id) use ($app,$blueridge) {

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $todo = $todoQr->findOneByTodoId($id);
    $params = $app->request()->post();


    
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');




    if(!empty($params))
    {   

        $userSessionId = base64_decode($_SESSION['user']);
        $blueridgeUser = $blueridge['documentManager']->find('\Blueridge\Documents\User', $userSessionId);
        $basecampClient = new Basecamp($blueridge);

        var_dump($basecampClient);
        // create new job
        // $result = Workman::updateTodo($todo,$params);

    }
    // print_r($body);


    // validate for ajax calls
    // $todo = $blueridge['documentManager']->findOneByTodoid('\Blueridge\Documents\Todo', $id);
    // echo json_encode($todo->toArray());

    // $body= $app->request()->getBody();
    // error_log($body);
    // var_dump($body);
    // $collection=null;        
    // if(!empty($id))
    // {
    //     $collection = $blueridge['documentManager']->find('\Blueridge\Documents\Todo', $id);
    // }

    // if(!empty($params['user']))
    // {
    //     // set query builder
    //     $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    //     $user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $params['user']);
    //     $collection = $todoQr->fetchByUser($user);   
    // }

    // if(empty($collection)){
    //     $app->response()->status(404);
    // }else{
    //     $resource = json_encode($collection->toArray());
    //     echo $resource;  
    // }

});