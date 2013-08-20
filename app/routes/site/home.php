<?php
/**
 * Home 
 */
$app->get('/',function() use ($app){    
    $app->render('site/home.html');
});