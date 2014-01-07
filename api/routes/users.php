<?php
/**
 * Users
 */
use Blueridge\Documents\User;


$app->get('/api/users/(:userId/)',function ($userId = null) use ($app,$blueridge) {

    // validate for ajax calls
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');

    // $params = $app->request()->get();
    $collection=Array();
    if(!empty($userId))
    {
        $collection = $userQr->findOneById($userId)->toArray();   
    }else{

        $users = $userQr->fetchAll();
        foreach ($users as $user) {
            $collection[] = $user->toArray();
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