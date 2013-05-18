<?php
/**
 * BlueRidge App
 */

require '../vendor/autoload.php';

use \Slim\Slim;


$app = new Slim(array(
	'templates.path' => '../templates'
	));

$app->get('/',function() use ($app){
	$app->render('home.php');
});

$app->get('/auth/',function() use ($app){
	/**
	 * @todo check for session
	 */
	// redirect them to basecamp
	$app->redirect('https://launchpad.37signals.com/authorization/new?type=web_server&client_id=e391c424f7787e13c608bda67a22c2b121e50418&redirect_uri=http://blueridgeapp.com/basecamp/');
});

$app->get('/basecamp/',function() use ($app){

	$code = $app->request()->get('code');
	if(!empty($code)){
		$basecamp = new \BlueRidge\Services\Basecamp();
		$authToken= $basecamp->getAuthToken($code);
		$authorization = $basecamp->getAuth($authToken);

		$api = new \BlueRidge\Services\BlueRidgeApi();
		$user=$api->createUser($authToken,$authorization);
		error_log($authToken);
		error_log($authorization);
		$url = "/todos/{$user->id}";
		$app->redirect($url);		
	}

});

$app->get('/xprs/',function() use ($app){
	$token = "BAhbByIB03siZXhwaXJlc19hdCI6IjIwMTMtMDUtMzFUMTY6NDk6NDVaIiwidXNlcl9pZHMiOlsxNDMyMTM0Myw3NjYxMTk2LDEzNjA3Nzg1LDEzNjA4MDM1LDE0MzQ0NTYyXSwiY2xpZW50X2lkIjoiZTM5MWM0MjRmNzc4N2UxM2M2MDhiZGE2N2EyMmMyYjEyMWU1MDQxOCIsInZlcnNpb24iOjEsImFwaV9kZWFkYm9sdCI6IjQyYjMwZTYyZjE3MzAwYTRhYjgxNTY0OGQ5MmZkNTIwIn11OglUaW1lDfBTHMAXedDG--9b2116ea347caf8fac3b350d111ad2f4e774d5c0";

	$url = "https://launchpad.37signals.com/authorization.json";
	$headers = [
	'Authorization'=>"Bearer {$token}",
	'User-Agent'=>'BlueRidgeApp (api@blueridgeapp.com)'
	];

	//
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'GET',
			'header' => "Authorization: Bearer {$token}"
			)
		));
	$data = file_get_contents($url, false, $context);
	//
	var_dump($data);
});


$app->get('/login/',function() use ($app){
	$app->render('login.php');
});
$app->post('/login/',function() use ($app){
	
	//$app->render('login.php');
});
$app->get('/todos/:userid/',function($userid=null) use ($app){
	if(!empty($userid)){
		$api = new \BlueRidge\Services\BlueRidgeApi();
		$todos = $api->fetchTodos($userid);
	}
	$app->render('todos.php',array("todos"=>$todos));
});
$app->get('/people/',function() use ($app){
	$app->render('people.php');
});


$app->run();