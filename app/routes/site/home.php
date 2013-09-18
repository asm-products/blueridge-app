<?php
/**
 * Routes for the Website
 */

$app->get('/',function() use ($app){
    $app->render('site/home.html');
});
$app->response->headers->set('Content-Type', 'text/html');
