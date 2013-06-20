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

$app->get('/api/providers/:name', function ($name) use ($app) {
	
	if(isset($app->providers->$name)){
		$providerName  = "\\BlueRidge\\Providers\\{$app->providers->$name->handler}";
	}else{
		return ;
	}

	$provider = new $providerName($app);
	$collection = $provider->toArray();
	echo json_encode($collection);
});

$app->post('/api/users', function () use ($app) {	
	
	$params = json_decode($app->request()->getBody());
	$code = $params->code;
	$providerName = $params->provider;

	if(isset($app->providers->$providerName)){
		$handler  = "\\BlueRidge\\Providers\\{$app->providers->$providerName->handler}";
	}else{
		return ;
	}

	$provider = new $handler($app); 
	$token = $provider->authorize($code);

	$auth = $provider->getAuthorization($token);
	$accounts = $provider->getProjectAccounts($auth,$token);
	$me= $provider->getMe($auth,$token);

	$user = new User($app);
	$service_properties = ['providers'=>["{$providerName}"=>['auth'=>$auth]],'accounts'=>$accounts];
	$properties = array_merge($me,$service_properties);
	$resource = $user->create($properties);
	//if(empty($resource)){

	//}
	
	//$app->response()->status(201);
	echo (json_encode((object) ['id'=>$user->id,'key'=>$user->key]));
	
});

$app->put('/api/users/:id',function($id) use ($app){
	$params = json_decode($app->request()->getBody());
	$user=new User($app);
	$user->update(["id"=>$params->id],$params,true);

	//do a responce check
});

$app->get('/api/:resource(/:id)', function ($resource,$id = null) use ($app) {
	
	$entityName  = "\\BlueRidge\\Entities\\{$app->resource->entity}";

	$entity= new $entityName($app);
	if(!empty($id)){
		$params=['id'=>$id,$app->request()->get()];
		$entity->fetchOne($params);		

		$collection = $entity->toArray();
	}else{
		$collection = $entity->fetch($app->request()->get());
	}

	if(empty($collection)){
		$app->response()->status(404);
	}

	$resource = json_encode($collection);
	echo $resource;
	
});

$response = $app->response();
$response['Content-Type'] = 'application/json';
$app->run();