<?php
/**
 * Routes for the Website
 */
$app->get('/',function() use ($app){
    $app->render('site/home.html');
});

$app->get('/preview(/)',function() use ($app){
    $app->render('site/preview.html');
});