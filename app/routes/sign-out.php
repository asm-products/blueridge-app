<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

$app->get("/sign-out/", function () use ($app,$blueridge) {
	$blueridge['authenticationService']->clearIdentity();	
	$app->redirect('/');
});