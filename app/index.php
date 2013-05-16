<?php
/**
 * BlueRidge App
 */

require '../vendor/autoload.php';

use \Slim\Slim;


$app = new Slim();

$app->get('/',function() use ($app){
	/**
	 * @todo check for session
	 */
	// redirect them to basecamp
	 $app->redirect('https://launchpad.37signals.com/authorization/new?type=web_server&client_id=e391c424f7787e13c608bda67a22c2b121e50418&redirect_uri=http://dev-www.blueridgeapp.com/auth');
});

$app->get('/auth',function() use ($app){

	echo "booya";
});
/*

$app->get('/:resource/(:id/)', function ($resource,$id = null) use ($app) {

	$entityName  = "\\BlueRidge\\Entities\\{$app->resource->entity}";

	$user= new $entityName;
	$user->init($app);
	if (!empty($id)){

	}else{
		$collection = $user->fetch();
		$resourse = json_encode($collection);
	}
	echo $resourse;
});
*/
$app->run();