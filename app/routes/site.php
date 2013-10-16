<?php

/**
 * Home page
 */

$app->get('/',function() use ($app){
    $app->render('site/home.html');
});
$app->response->headers->set('Content-Type', 'text/html');

/**
 * Pages on the root
 */
$app->get('/:page/',function($page) use ($app){
    $allowed_routes = ['pricing','preview','privacy','about'];
    if(in_array($page, $allowed_routes))
    {
        $app->render("site/{$page}.html", array('routeName' => $page));    
    }else{
        $app->render("common/error-404.html",['message'=>'No Joy. File not found',404]);
    }

});
$app->response->headers->set('Content-Type', 'text/html');
