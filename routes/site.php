<?php
/**
 * Routes for the Website
 */
$app->get('/',function() use ($app){
    $app->render('site/home.html');
});

$app->get('/preview/',function() use ($app){
    $app->render('site/preview.html');
});

$app->get('/pricing/',function() use ($app){
    $app->render('site/pricing.html');
});

$app->get('/privacy/',function() use ($app){
    $app->render('site/privacy.html');
});

$app->get('/about/',function() use ($app){
    $app->render('site/about.html');
});

$app->get('/support/',function() use ($app){
    $app->render('site/support.html');
});