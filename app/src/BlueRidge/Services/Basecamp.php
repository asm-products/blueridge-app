<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Services;

class Basecamp  
{
	private $app;
	protected $service;
	protected $expire;

	public function __construct($app,$service){
		$this->app = $app;
		$this->service = $service;
		// set cache expire
		$date = new \DateTime($service['user']['expires_at']);
		$this->expire = $date->getTimestamp();
	}

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
		$authorization = file_get_contents($url, false, $context);
		return $authorization;
	}

	public function getToDos(){

		$toDos = array();


		// get all todolists
		$toDoListItems = $this->getToDoListItems();

		foreach($toDoListItems as $toDoListItem){
			$toDoList = $this->app->cache->get("todo-list-{$toDoListItem->id}");
			if(empty($toDoList)){
				$toDoList = $this->fetch($toDoListItem->url);
				$this->app->cache->add("todo-list-{$toDoList->id}",$toDoList,false,$this->expire);
			}	
			
			foreach ($toDoList->todos->remaining as $toDoItem){

				$toDo = $this->app->cache->get("todo-{$toDoItem->id}");
				if(empty($toDo)){
					$toDoItem->project = $toDoListItem->bucket->name;
					$toDoItem->list = $toDoListItem->name;
					$toDoItem->siteUrl=$this->getSiteUrl($toDoItem->url);
					$toDo = $toDoItem;
					$this->app->cache->add("todo-{$toDo->id}",$toDo,false,$this->expire);	
				}
				$toDos[]=$toDo;
			}
		}
		return $toDos;
	}

	protected function getToDoListItems(){
		$endpoint = "todolists.json";
		$toDoLists=array();
		foreach ($this->service['user']['accounts'] as $account) {

			if($account['product']=='bcx'){
				
				$toDoLists = $this->app->cache->get("todo-lists-{$account['id']}");
				if($toDoLists===false){
					$toDoLists= $this->fetch("{$account['href']}/{$endpoint}");
					if($toDoLists!==null){
						$this->app->cache->add("todo-lists-{$account['id']}",$toDoLists,false,$this->expire);
					}
				}
			}
		} 

		return $toDoLists;
	}

	protected function getSiteUrl($url){
		$points = ['/api/v1','.json'];
		$siteUrl = str_replace($points,'',$url);
		return $siteUrl;
	}
	
	protected function fetch($url){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "BlueRidgeApp (api@blueridgeapp.com)");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer {$this->service['auth']['access_token']}"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data=curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		return $data;
	}

}
