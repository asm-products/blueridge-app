<?php
/**
 * BlueRidge App Api
 */

require '../vendor/autoload.php';

use \Slim\Slim;
use \BlueRidge\Init;
use \BlueRidge\Entities\User;
use \BlueRidge\Entities\ToDo;
use \BlueRidge\Entities\Service;


$app = new Slim();
$app->add(new Init());

/**
 * Index Route
 * Show docs
 */
$app->get('/api',function() use ($app){
	echo 'api hello';
});


$app->post('/api/users',function() use ($app){
	$authToken = $app->request()->post('authToken');
	$auth = $app->request()->post('authorization');
	$user = new User();
	$user->init($app);
	$service = $app->request()->post('service');
	$user=$user->create($authToken,$auth)->toArray();

	// Add the user todos and people to jobs
	
	$resource = json_encode($user);
	echo $resource;
});

$app->get('/api/providers/:name', function ($name) use ($app) {
	
	if(isset($app->services->$name)){
		$providerName  = "\\BlueRidge\\Providers\\{$app->services->$name->provider}";
	}else{
		return ;
	}

	$provider = new $providerName($app);
	$collection = $provider->toArray();
	echo json_encode($collection);
});

$app->post('/api/providers/:name', function ($name) use ($app) {


	echo file_get_contents('../data/auth.json');
	/**
	 * @todo validate for Ajax requests
	 */
	/*
	$code = $app->request()->post('code');

	if(isset($app->services->$name)){
		$providerName  = "\\BlueRidge\\Providers\\{$app->services->$name->provider}";
	}else{
		return ;
	}

	$provider = new $providerName($app); 
	$token = $provider->authorize($code);
	$auth = $provider->getAuthorization($token);
	$me= $provider->getMe($auth,$token);
	$projects = $provider->getProjects($auth,$token);

	$user = new User($app);
	$service_properties = ['services'=>["{$name}"=>['auth'=>$auth]],'projects'=>$projects->projects];
	$properties = array_merge($me,$service_properties);
	$resource=$user->create($properties)->toArray();
	echo json_encode($resource);
	*/		
});

$app->get('/api/:resource(/:id)', function ($resource,$id = null) use ($app) {
	
	$entityName  = "\\BlueRidge\\Entities\\{$app->resource->entity}";

	$entity= new $entityName($app);
	if(!empty($id)){
		$params=['id'=>$id,$app->request()->get()];
		$collection = $entity->fetchOne($params)->toArray();
	}else{
		$collection = $entity->fetch($app->request()->get());
	}
	$resource = json_encode($collection);
	echo $resource;
	
});

$app->run();