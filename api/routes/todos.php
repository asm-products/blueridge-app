<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

use Blueridge\Documents\Todo;
use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;
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
    $user = $userQr->find($userid);

    if (empty($user)) {
        $app->response()->status(404);
        return;
    }

    if (empty($user->profile['projects'])) {
        $app->response()->status(404);
        return;
    }

    $userTodos = Array();
    Resque::enqueue('sync', 'Blueridge\Jobs\FilterBasecampTodosByUser', ['userid'=>$user->id]);
    Resque::enqueue('sync', 'Blueridge\Jobs\FetchBasecampTodosByUser', ['userid'=>$user->id]);

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $todos = $todoQr->fetchByUser($user)->toArray();


    $todos = array_map(function($todo){
        return $todo->toView();
    },array_values($todos));

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
    $todo = $todoQr->findOneById($id);

    $basecampClient = new Basecamp($blueridge);
    $result = $basecampClient->updateTodo($user,$todo,$payload);

    if($result==200){
        $blueridge['documentManager']->remove($todo);
        $blueridge['documentManager']->flush();
    }
    $app->response()->status($result);

});