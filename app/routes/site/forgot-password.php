<?php
/**
 * Forgot Password
 */


$app->get('/forgot-password/',function() use ($app){
    $app->render("site/forgot-password.html");

});

$app->post('/forgot-password/',function() use ($app){

});


$app->response->headers->set('Content-Type', 'text/html');