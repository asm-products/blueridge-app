<?php

$url = "http://dev-api.blueridgeapp.com/users/";
$params = [
'authToken'=>'authtoken data',
'authorization'=>'auth data',
'service'=>'basecamp'
];

$ch = curl_init($url);

$params = http_build_query($params);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_HEADER,0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
$user = curl_exec($ch);
curl_close($ch);
//$user = json_decode($user);
var_dump($user);
//return $user;