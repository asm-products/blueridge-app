<?php
/**
 * Sign In Routes
 */
use  \Blueridge\Documents\User;
use  \Blueridge\Utilities\Doorman;


$app->get("/sign-out/", function () use ($app) {
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
    $app->render('site/sign-out.html');
});