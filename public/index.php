<?php
/**
 * BlueRidge App
 */

require '../vendor/autoload.php';

use \Slim\Slim;
use \BlueRidge\Init;
use \BlueRidge\Entities\User;
use \BlueRidge\Entities\ToDo;
use \BlueRidge\Services\Basecamp;

$app = new Slim();
$app->add(new Init());

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
		$basecamp = new Basecamp();
		$authToken= $basecamp->getAuthToken($code);
		$authUser = $basecamp->getAuth($authToken);

		$user= new User();
		$user->init($app);

		$currentUser=$user->create($authToken,$authUser);
		//var_dump($user);
		//var_dump($currentUser);
		//exit();
		$url = "/todos/{$currentUser['id']}";
		$app->redirect($url);		
	}else{
		//redirect with a fail 500 Error
		$app->redirect('/fail/',500);
	}

});

$app->get('/login/',function() use ($app){
	$app->render('login.php');
});
$app->post('/login/',function() use ($app){
	//login user
	//$app->render('login.php');
});
$app->get('/todos/:userid/',function($userid=null) use ($app){
	if(!empty($userid)){
		$todo = new ToDo();
		$todo->init($app);
		$todos=$todo->fetch($userid);
	}
	$app->render('todos.php',array("todos"=>$todos));
});
$app->get('/people/',function() use ($app){
	$app->render('people.php');
});


$app->run();