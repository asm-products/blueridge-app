<?php
/**
 * BlueRidge App Api
 */

namespace BlueRidge\Services;

class BlueRidgeApi 
{
	/**
	 * @deprecated
	 * Moved to app User Class
	 */
	public function createUser($authToken,$authorization){

		$url = "http://api.blueridgeapp.com/users/";
		$params = [
		'authToken'=>$authToken,
		'authorization'=>$authorization,
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
		$user = json_decode($user);
		return $user;

	}
	/**
	 * @deprecated
	 */
	public function fetchTodos($userid){

		$url = "http://api.blueridgeapp.com/todos/?user={$userid}";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$todos = curl_exec($ch);
		curl_close($ch);
		$todos = json_decode($todos);
		return $todos;


	}
}