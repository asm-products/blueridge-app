<?php 
/**
 * Users
 */
use \BlueRidge\Entities\User;
use \BlueRidge\Crew\Mailman;
use \BlueRidge\Crew\Butler;

/**
 * Get User
 */
$app->get('/api/users(/:id(/:segment))', function ($id = null,$segment = null) use ($app) {
	
	$collection = new \StdClass();
	$user= new User($app);
	if(!empty($id)){
		$params=['id'=>$id,$app->request()->get()];

		if(!empty($segment)){
			$collection->$segment = $user->fetchOneById($id)->fetchSegment($segment);
		}else{
			$user->fetchOne($params);		
			$collection = $user->toArray();
		}		

	}else{
		$collection->users = $user->fetch($app->request()->get());
	}

	if(empty($collection)){
		$app->response()->status(404);
	}

	$resource = json_encode($collection);
	echo $resource;
	
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
	echo (json_encode((object) ['id'=>$user->id,'init'=>true]));

	// set access 

	// send email
	/**
	 * hi new user your password is ..wasup`
	 */
	$mailman = Mailman::send($app, $user,'welcome');

	
});

$app->put('/api/users/:id',function($id) use ($app){
	$params = json_decode($app->request()->getBody());
	$user=new User($app);
	$user->update(["id"=>$params->id],$params,true);

	//do a responce check
});

