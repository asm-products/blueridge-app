<?php
/**
 * Comments
 */
// PUT /api/todos/6556563/ - update
// POST - add new comment
use Blueridge\Documents\Todo;
use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp\BasecampClient;
use Blueridge\Providers\Basecamp\Helper as ServiceHelper;


$app->get('/api/comments/', function () use ($app,$blueridge) {

    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');

    $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

    $collection=Array();
    $todoid= $app->request()->get('todoid');
    if(empty($todoid)) {
        $app->response()->status(404);
        return;
    }

    $todo = $todoQr->findOneByTodoId($todoid);
    if (empty($todo)) {
        $app->response()->status(404);
        return;
    }

    $totalCount = $todo->source['comments_count'];
    $comments = array_slice($todo->source['comments'],0,3);


    $collection = ['count'=>$totalCount,'comments'=>$comments];
    echo json_encode($collection);
    $app->response->headers->set('Content-Type', 'application/json');

});