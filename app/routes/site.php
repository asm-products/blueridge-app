<?php
/**
 * Blueridge 
 * 
 * Site routes
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelbas.com>
 */

$app->get('/',function() use ($app,$blueridge){	
    $app->render('site/home.html');
});
$app->response->headers->set('Content-Type', 'text/html');

$app->get('/pricing/',function() use ($app){
    $app->render('site/pricing.html');
});
$app->response->headers->set('Content-Type', 'text/html');

$app->get('/preview/',function() use ($app){
    $app->render('site/preview.html');
});
$app->response->headers->set('Content-Type', 'text/html');

$app->get('/privacy/',function() use ($app){
    $app->render('site/privacy.html');
});

$app->get('/about/',function() use ($app){
    $app->render('site/about.html');
});

$app->response->headers->set('Content-Type', 'text/html');