<?php
/**
 * Get User
 */

use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;

$app->get('/app/projects/',$authenticate($app), function () use ($app,$blueridge) {

    $id = base64_decode($_SESSION['user']);
    $user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $id);
    
    if (empty($user)){
        unset($_SESSION['live']); 
        unset($_SESSION['user']);
        $app->redirect('/');  
    }
    // refresh projects
    $basecampClient = new Basecamp($blueridge);
    $user->projects = $basecampClient->getProjects($user);

    $blueridge['documentManager']->persist($user);
    $blueridge['documentManager']->flush();


    $projects = $user->projects;
    // Resque::enqueue('default', 'Blueridge\Jobs\Pull\Todos', ['userId'=>$user->id]);
    
    $app->render("app/projects.html", ['projects' => $projects,'route'=>'projects','plan'=>$user->toArray()['subscription']['plan']]);    
});

$app->post('/app/projects/',$authenticate($app),function() use ($app,$blueridge){

    $params = $app->request->post('selected');
    $params = array_map('intval', $params);
    $id = base64_decode($_SESSION['user']);
    $user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $id);
    $user->updateProfile('projects',$params);
    $user->url = "/users/{$user->id}";
    $blueridge['documentManager']->flush($user);
    
    Resque::enqueue('default', 'Blueridge\Jobs\Pull\Todos', ['userId'=>$user->id]);

    unset($_SESSION['noob']);    
    $app->redirect('/app/todos/');
});