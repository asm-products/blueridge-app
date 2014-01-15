<?php
/**
 * Blueridge 
 * 
 * Site routes
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

$app->get('/',function() use ($app,$blueridge){
    
    $view = ['mode'=>$app->mode,'connected'=>false];    
    
    if($app->getCookie('_blrg_connect')) {
        $view['connected']=true;
    }

    $app->render('site/home.html',$view);
    $app->response->headers->set('Content-Type', 'text/html');
});