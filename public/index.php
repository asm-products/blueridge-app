<?php
/**
 * BlueRidge App
 */

require '../vendor/autoload.php';

use \Slim\Slim;


$app = new Slim(array(
	'templates.path' => '../templates'
	));

$app->get('/',function() use ($app){
	$app->render('home.php');
});

$app->get('/auth/',function() use ($app){
	/**
	 * @todo check for session
	 */
	// redirect them to basecamp
	$app->redirect('https://launchpad.37signals.com/authorization/new?type=web_server&client_id=e391c424f7787e13c608bda67a22c2b121e50418&redirect_uri=http://blueridgeapp.com/basecamp/');
});

$app->get('/basecamp/',function() use ($app){

	$code = $app->request()->get('code');
	if(!empty($code)){
		$url = "https://launchpad.37signals.com/authorization/token";
		$params = [
		'type'=>'web_server',
		'client_id'=>'e391c424f7787e13c608bda67a22c2b121e50418',
		'redirect_uri'=>'http://blueridgeapp.com/basecamp/',
		'client_secret'=>'c0d00cc97adda78505784d56c3f2eae158db63aa',
		'code'=>$code
		];
		$ch = curl_init($url);


		$params = http_build_query($params);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 

		// grab URL and pass it to the browser
		$data = curl_exec($ch);

		if(!$data){
			echo 'no work';
			var_dump(curl_getinfo($ch));
			var_dump(curl_error($ch));
		}else{
			var_dump($data);
		}

		// close cURL resource, and free up system resources
		curl_close($ch);

	}
});

/*
$app->get('/app/(:resource)',function() use ($app){
	$app->render('app.php');
});
*/
$app->run();