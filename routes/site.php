<?php
/**
 * Routes for the Application
 */


$app->get('/:page/',function($page) use ($app){

    if (isset($_SESSION['user']))
    {
        $app->redirect('/app/todos/');
    }


    $allowed_routes = ['pricing','preview','privacy','about'];
    if(in_array($page, $allowed_routes))
    {
        $app->render("site/{$page}.html", array('routeName' => $page));    
    }


});
$app->response->headers->set('Content-Type', 'text/html');
