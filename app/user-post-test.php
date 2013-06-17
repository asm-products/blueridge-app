<?php
$url = "http://dev-api.blueridgeapp.com/users";
$client= new MongoClient("mongodb://208.68.39.158:27017");
$db=$client->selectDB('BlueRidge');
$users = new MongoCollection($db,"Users");	
$user = (object) $users->findOne(['email' => 'moses@mospired.com'],['name' => true, 'services' => true]);
$auth = json_encode($user->services['basecamp']['auth']);
$authorization = json_encode($user->services['basecamp']['user']);

$params = ['authToken'=>$auth,'authorization'=>$authorization,'service'=>'basecamp'];

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