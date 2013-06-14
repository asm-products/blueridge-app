<?php 
$basecamp_connect= ['name'=>'basecamp','authUrl'=>''];

$url = "http://dev-api.blueridgeapp.com/providers/basecamp";


if($_SERVER['REQUEST_METHOD']=='GET'){
	echo file_get_contents($url);	
}else if ($_SERVER['REQUEST_METHOD']=='POST'){

	$params = ['code'=>$_POST['code']];
	$ch = curl_init($url);
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