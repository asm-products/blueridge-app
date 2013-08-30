<?php
/**
 *  Auth Basecamp
 */

$app->get('/auth/basecamp/',function() use ($app){
    $code = $app->request()->params('code');
    $basecampSettings = $app->config('providers')['basecamp'];

    if(empty($code))
    {        
        if(!empty($basecampSettings)){
            $auth_request = "{$basecampSettings['auth_url']}?client_id={$basecampSettings['client_id']}&redirect_uri={$basecampSettings['redirect_uri']}&type=web_server";        
            $app->redirect($auth_request);
        }else{
            $app->render("common/error-500.html",['message'=>'Looks like we have a problem connecting you to Basecamp',500]);
        }
    }else
    {
        

    }
    var_dump($code);
    exit();


});

$app->response->headers->set('Content-Type', 'text/html');