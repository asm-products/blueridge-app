<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
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
    $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

    $basecampClient = new Basecamp($blueridge);
    $basecampProjects = $basecampClient->getProjects($user);
    $user= $userQr->updateProjects($user, $basecampProjects);
    $projects = $user->projects;
    $userDetails = $user->toArray();

    $view = [
        'user'=>$userDetails,
        'projects' => $projects,
        'route'=>'projects',
        'plan'=>$userDetails['subscription']['plan'],
        'mode'=>$app->mode
    ];

    $app->render("app/projects.html", $view);
});

$app->post('/app/projects/',function() use ($app,$blueridge){

    $params = $app->request->post('selected');
    $params = array_map('intval', $params);

    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

    $userQr->updateProfile($user,'projects',$params);
    $userQr->setStatus($user,"active");


    Resque::enqueue('sync', 'Blueridge\Jobs\FilterBasecampTodosByUser', ['userid'=>$user->id]);
    Resque::enqueue('sync', 'Blueridge\Jobs\FetchBasecampTodosByUser', ['userid'=>$user->id]);

    $app->redirect('/app/todos/');
});