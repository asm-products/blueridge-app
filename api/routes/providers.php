<?php
/**
 * Providers 
 */
$app->get('/api/providers/:name', function ($name) use ($app) {
	
	if(isset($app->providers->$name)){
		$providerName  = "\\BlueRidge\\Providers\\{$app->providers->$name->handler}";
	}else{
		return ;
	}

	$provider = new $providerName($app);
	$collection = $provider->toArray();
	echo json_encode($collection);
});