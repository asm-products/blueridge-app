<?php 
/**
 * Access
 */
use \BlueRidge\Entities\Access;

/**
 * Get Access
 */
$app->get('/api/accesses(/:id)', function ($id = null) use ($app) {

    /**
     * @todo populate all accesses
     */

    /*
    $collection = new \StdClass();
    $access= new Access($app);
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
    */
    
});

$app->post('/api/accesses', function () use ($app) {   
    
    $params = json_decode($app->request()->getBody());
    $email = $params->email;
    $password = $params->password;

    
    //hash = hmac.sha256(email+password) 

    $user = new User($app);
    $user->fetchOne(['email'=>$email]);

    $access = new Access($app);

    /*

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
    */
    
});
