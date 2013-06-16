<?php
$url = "http://dev-www.blueridgeapp.com/api/users";
$params = ['code'=>$_GET['code'],'provider'=>$_GET['provider']];

$ch = curl_init($url);
$params = http_build_query($params);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_HEADER,0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
$user = curl_exec($ch);
curl_close($ch);

print_r($user);