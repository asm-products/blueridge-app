<?php
/**
 * Routes for the Application
 */
$app->get('/',function() use ($app){
    $app->render('site/home.html');
});