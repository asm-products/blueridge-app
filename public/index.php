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
		$api = new \BlueRidge\Services\BlueRidgeApi();
		$auth= $basecamp->getToken($code)->getAuth();
	
		var_dump($auth);		
		//$user=$api->setUser($token);		
	}

});


$app->get('/login/',function() use ($app){
	$app->render('login.php');
});
$app->post('/login/',function() use ($app){
	
	//$app->render('login.php');
});
$app->get('/todos/',function() use ($app){
	$app->render('todos.php');
});
$app->get('/people/',function() use ($app){
	$app->render('people.php');
});


$app->run();