<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;

/**
 * Display projects
 * @param string '/app/projects/' 
 * @param type function () use ($app 
 * @param type $blueridge 
 * @return type
 */
$app->get('/app/projects/',function () use ($app,$blueridge) {

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());

    if(empty($user)){
        $app->redirect('/sign-out/');
    }
    
    
    $basecampClient = new Basecamp($blueridge);
    $basecampProjects = $basecampClient->getProjects($user);
    $user= $userQr->updateProjects($user, $basecampProjects);    
    $projects = $user->projects;    
    $userDetails = $user->toArray();    
    
    $app->render("app/projects.html", ['user'=>$userDetails,'projects' => $projects,'route'=>'projects','plan'=>$userDetails['subscription']['plan']]);    
});

/**
 * 
 */
$app->post('/app/projects/',function() use ($app,$blueridge){

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User'); 
    $user = $userQr->findOneByEmail($blueridge['authenticationService']->getIdentity());

    if(empty($user)){
        $app->redirect('/sign-out/');
    }

    $params = $app->request->post('selected');
    $params = array_map('intval', $params);


    $userQr->updateProfile($user,'projects',$params);
    $userQr->updateUrl($user,"/users/{$user->id}");
    $userQr->updateStatus($user,"active");


    // Resque::enqueue('default', 'Blueridge\Jobs\Utils\CleanUpTodos', ['userId'=>$user->id,'projects'=>$params]);
    // Resque::enqueue('default', 'Blueridge\Jobs\Pull\Todos', ['userId'=>$user->id,'projects'=>$params]);

    $app->redirect('/app/todos/');
});