<?php 

$app_env = getenv('APPLICATION_ENV');
$env= ($app_env)?$app_env:"production";  
$configs = getConfigs($env);

if($_SERVER['REQUEST_METHOD']=='GET'){
	echo json_encode($configs->basecamp);
}else if ($_SERVER['REQUEST_METHOD']=='POST'){
	$params = ['code'=>$_POST['code']];
	$ch = curl_init($configs->api);
	$params = http_build_query($params);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
	curl_setopt($ch, CURLOPT_HEADER,0);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
	$results = curl_exec($ch);
	curl_close($ch);

	echo $results;
}
function getConfigs($env){
	$config_file = "../config.json";
	$config_content= file_get_contents($config_file); 
	$configs =json_decode($config_content); 
	return $configs->$env;
}