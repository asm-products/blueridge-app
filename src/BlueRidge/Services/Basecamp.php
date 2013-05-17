<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Service;

class Basecamp 
{
	protected $auth;

	public function getToken ($authCode){
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

		$auth = curl_exec($ch);
		curl_close($ch);
		$this->auth = $auth;		
		return $this;

	}
	public function getAuth(){

		$url = "https://launchpad.37signals.com/authorization.json";
		$headers = [
		'Authorization'=>"Bearer {$this->auth->access_token}",
		'User-Agent'=>'BlueRidgeApp (api@blueridgeapp.com)'
		];
		$ch = curl_init($url);



		curl_setopt($ch, CURLOPT_GET, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 

		$auth = curl_exec($ch);
		curl_close($ch);
		$this->auth = $auth;		
		return $this;

	}
}