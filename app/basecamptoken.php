<?php 

$code = $_REQUEST['code'];

$url = "https://launchpad.37signals.com/authorization/token";
$params = [
'type'=>'web_server',
'client_id'=>'e391c424f7787e13c608bda67a22c2b121e50418',
'redirect_uri'=>'http://blueridgeapp.com/auth',
'client_secret'=>'c0d00cc97adda78505784d56c3f2eae158db63aa',
'code'=>$code
];
$ch = curl_init($url);

$params = http_build_query($params);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL

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
