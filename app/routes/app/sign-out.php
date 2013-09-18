<?php
/**
 * Sign In Routes
 * @deprecated
 */
use  \Blueridge\Documents\User;
use  \Blueridge\Utilities\Doorman;


$app->get("/sign-out/", function () use ($app) {
    unset($_SESSION['live']);
    $app->render("site/sign-out.html");
});