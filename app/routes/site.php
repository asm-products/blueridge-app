<?php
/**
 * Routes for the Website
 */
$app->get('/',function() use ($app){
    $app->render('site/home.html');
});

$app->get('/(:route/)',function($route) use ($app){
    $app->render("site/{$route}.html", array('routeName' => $route));
});

$app->response->headers->set('Content-Type', 'text/html');