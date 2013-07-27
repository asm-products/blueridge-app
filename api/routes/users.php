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

	$user = new User($app);
	$service_properties = ['providers'=>["{$providerName}"=>['auth'=>$auth]],'accounts'=>$accounts,'projects'=>$projects];
	$properties = array_merge($me,$service_properties);

	$existing_user = $user->fetchOne(['email'=>$me['email']]);
	

	if(empty($existing_user)){
		
		$access = doorman_welcome();

		$properties['selected_projects']=[];
		$properties['key']=$access['key'];

		$user->create($properties);
		echo (json_encode((object) ['id'=>$user->id,'authorized'=>true,'init'=>true]));
		
		
		$mailman = \postman_send($app, $user,['password'=>$access['pass']]);

	}else{

		$user->refresh($service_properties);
		echo (json_encode((object) ['id'=>$user->id,'authorized'=>true,'updated'=>true]));
	}
	

});

$app->put('/api/users/:id(/:segment)',function($id,$segment=null) use ($app){
	$params = json_decode($app->request()->getBody());
	$user=new User($app,['id'=>$id]);


	$resource= $user->update($params);

	if($resource){
		$app->response()->status(200);
	}else{
		$app->response()->status(400);
		echo (json_encode((object) ['error'=>'Update failed. Contact support']));
	}
});

