<?php
/**
 * Subscriptions
 */
$app->post('/api/subscriptions', function () use ($app) {   

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

    $accounts = $provider->getProjectAccounts($auth,$token);


    $user = new User($app);
    $service_properties = ['providers'=>["{$providerName}"=>['auth'=>$auth]],'accounts'=>$accounts];
    $properties = array_merge($me,$service_properties);
    $resource = $user->create($properties);

    echo (json_encode((object) ['id'=>$user->id,'init'=>true,'authorized'=>true]));

    $access = doorman_welcome();
    $user->update(["id"=>$user->id],['key'=>$access['key']],true);

    
    $mailman = \postman_send($app, $user,['password'=>$access['pass']]);    
    
    
});