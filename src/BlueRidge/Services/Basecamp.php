<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Services;

class Basecamp 
{

	public function getAuthToken ($authCode){
		$url = "https://launchpad.37signals.com/authorization/token";
		$params = [
		'type'=>'web_server',
		'client_id'=>'e391c424f7787e13c608bda67a22c2b121e50418',
		'redirect_uri'=>'http://blueridgeapp.com/basecamp/',
		'client_secret'=>'c0d00cc97adda78505784d56c3f2eae158db63aa',
		'code'=>$authCode
		];
		$ch = curl_init($url);


		$params = http_build_query($params);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 

		$authToken = curl_exec($ch);
		curl_close($ch);	
		return $authToken;

	}
	public function getAuth($authToken){

		$auth = json_decode($authToken);

		$url = "https://launchpad.37signals.com/authorization.json";
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'GET',
				'header' => "Authorization: Bearer {$auth->access_token}"
				)
			));
		$data = file_get_contents($url, false, $context);
		$authorization = json_decode($data,true);
		return $authorization;
	}
}
