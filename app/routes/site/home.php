<?php
/**
 * Routes for the Website
 */

$app->get('/',function() use ($app){
    if (isset($_SESSION['live']))
    {
        $app->redirect('/app/todos/');
    }

    $app->render('site/home.html');
});
$app->response->headers->set('Content-Type', 'text/html');
