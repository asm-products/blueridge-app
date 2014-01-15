<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Documents\Todo;
use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp\BasecampClient;
use Blueridge\Providers\Basecamp\Helper as ServiceHelper;


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
    
    if (empty($user)) {
        $app->response()->status(404);
        return;
    }

    if (empty($user->profile['projects'])) {
        $app->response()->status(404);
        return;
    }

    $userTodos = Array();
    foreach ($user->profile['projects'] as $selectedProject) {

        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
        $projectTodos = $todoQr->fetchByProject($user,$selectedProject)->toArray();

        if(empty($todos)) {
            $providerConfigs = $blueridge['configs']['providers']['basecamp']; 
            $service = BasecampClient::factory([
                'token'    => $user->providers['basecamp']['token']['access_token'],
                'user_agent' => $providerConfigs['user_agent'],
                ]);
            $serviceHelper = new ServiceHelper($service); 
            $todoItems = $serviceHelper->fetchTodos($user,[$selectedProject]);            
            if(!empty($todoItems)):
            $todoIterator = new \ArrayIterator($todoItems); 
            foreach($todoIterator as $todoItem){

                $todoItem['todoId']=$todoItem['rel']['project']['account']['product'].'_'.$todoItem['id'];        
                $todoItem['source'] = $service->getTodo([
                    'accountId'=>$todoItem['rel']['project']['account']['id'],
                    'projectId'=>$todoItem['rel']['project']['id'],
                    'todoId'=>$todoItem['id'],                
                    ]);
                unset($todoItem['id']);
                $todo = $todoQr->findOneBy(['todoId'=>$todoItem['todoId']]);
                if (empty($todo)) {
                    $todo = new Todo();
                    $todoItem  = $todo->polish($todoItem);
                    $todo->setProperties($todoItem);        
                    $blueridge['documentManager']->persist($todo);
                    $blueridge['documentManager']->flush();
                }           
            // Resque::enqueue('activity', 'Blueridge\Jobs\Pull\SaveTodoDocument', ['todoItem'=>$todoItem]);   
            }
            endif;

            $projectTodos = $todoQr->fetchByProject($user,$selectedProject)->toArray();        
        }
        $userTodos = array_merge($userTodos,$projectTodos);
    }

    $todos = array_map(function($todo){
        return $todo->toArray();
    },array_values($userTodos));
    
    $collection = ['count'=>count($todos),'projects'=>count($user->profile['projects']),'todos'=>$todos];
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