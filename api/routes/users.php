<?php 
/**
 * Users
 */
use \BlueRidge\Entities\User;

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
	$me= $provider->getMe($auth,$token);

	$accounts = $provider->getAccounts($auth,$token);
	$projects = $provider->getProjects($auth,$token);
	$access = doorman_welcome();

	$user = new User($app);
	$service_properties = [
	'key'=>$access['key'],
	'providers'=>["{$providerName}"=>['auth'=>$auth]],
	'subscription'=>[
		'plan'=>'free',
		'payment'=>[]
		],
	'profile'=>[
		'accounts'=>$accounts,
		'projects'=>[]
		],
	'projects'=>$projects
	];

	$properties = array_merge($me,$service_properties);
	$response =$user->create($properties);

	if($response['status'] == 500){
		$app->response()->status(500);
		echo (json_encode($response['message']));
	}else{
		$app->response()->status($response['status']);
		if ($response['status']==200){
			echo (json_encode((object) ['id'=>$user->id,'authorized'=>true,'updated'=>true]));
		}else{
			echo (json_encode((object) ['id'=>$user->id,'authorized'=>true,'init'=>true]));	
		}
	}
	

});

$app->put('/api/users/:id',function($id) use ($app){
	$params = json_decode($app->request()->getBody(),true);
	$user=new User($app);
	unset($params['id']);
	
	list($segment,$subset) = each($params);
	print_r($segment);
	print_r($subset);
	exit();

	
	$response= $user->update($id,$params);

	$app->response()->status($response['status']);	
	echo (json_encode($response['message']));
	
});

