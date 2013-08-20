<?php
/**
 * Routes for the Application
 */
$app->get('/app/(:route/)',function($route) use ($app){
    $app->render("app/{$route}.html");
});
$app->response->headers->set('Content-Type', 'text/html');

/**
 * @todo 
 */
/*
$app->get('/app/export/(:format)', function ($format) {
    //$app->render("app/{$route}.html");
    $response = $app->response();
    //$response['Content-Type'] = 'application/json';
});

*/