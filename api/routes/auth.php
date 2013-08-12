<?php 
/**
 * Access
 */
use \BlueRidge\Entities\User;
use  \BlueRidge\Utilities\Doorman;

$app->post('/api/auth', function () use ($app) {   

    $params = json_decode($app->request()->getBody());
    
    if(empty($params->email) || empty($params->passwd)){
        $app->response()->status(403);
        echo (json_encode((object) ['error'=>"Not Authorized",'message'=>"Missing Credential"]));
    }else{
        $email = $params->email;
        $password = $params->passwd;



        $user = new User($app);
        $user->fetchOne(['email'=>$email]);
        $authorization = Doorman::authorize($password,$user->key);

        if (empty($authorization)){
            $app->response()->status(403);
            echo (json_encode((object) ['email'=>$email,'authorized'=>$authorization]));

        }else{
            $app->response()->status(200);
            echo (json_encode((object) ['id'=>$user->id,'init'=>false,'authorized'=>$authorization]));
        }
    }

});
