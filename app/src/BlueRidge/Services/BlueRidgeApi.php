<?php
/**
 * BlueRidge App Api
 */

namespace BlueRidge\Services;

class BlueRidgeApi 
{
	/*
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
	*/

	public function create($url,$params){
		$params = http_build_query($params);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		return $user;

	}

	//public function getUserTodos($userid){

		//$url = "http://api.blueridgeapp.com/todos/?user={$userid}";

		/*
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$todos = curl_exec($ch);
		curl_close($ch);
		$todos = json_decode($todos);
		return $todos;
		*/

	//}

		public function fetch($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, "BlueRidgeApp (api@blueridgeapp.com)");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data=curl_exec($ch);
			curl_close($ch);
			$data = json_decode($data);
			return $data;

		}
	}