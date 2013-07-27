<?php
/**
 * Subscriptiuserons
 */
use \BlueRidge\Entities\User;

$app->post('/api/subscriptions', function () use ($app) {   

    $params = json_decode($app->request()->getBody());

    if(empty($params->user) || empty($params->payment) || empty($params->plan)){
        $app->response()->status(402);
        echo (json_encode((object) ['error'=>"Payment Required",'message'=>"Missing payment details"]));
    }else{

        $user = new User($app);
        $user->fetchOne(['id'=>$params->user]);
        $customer = \cashier_subscribe($app,$user,$params);

        if (!empty($customer)){
            $app->response()->status(200);
            $user->update(["id"=>$user->id],['plan'=>$params->plan,'subscription'=>$customer],true);    
            echo (json_encode((object) ['id'=>$user->id,'subscribed'=>true]));
        }else{

            $app->response()->status(404);
            echo (json_encode((object) ['error'=>"Subscription failed",'message'=>"Contact BlueRidge Team"]));
        }
    }
    
});