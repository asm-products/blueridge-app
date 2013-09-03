<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Providers;

use \BlueRidge\ModelAbstract;

class BasecampClient
{

	protected $user_agent;
	protected $client_id;
	protected $client_secret;
	protected $redirect_uri;
	protected $auth_url;
	protected $token_url;
	protected $token;
	protected $accounts =[];
	protected $identity;

	public function __construct($settings)
	{		
		$this->setProperties($settings);
	}

	/**
	 * Set Properties
	 */
	protected function setProperties($properties)
	{
		foreach($properties as $property => $value){
			if (property_exists($this, $property)) {
				if($property == "auth_url"){
					$this->auth_url = $this->getAuthUrl($properties);
				}else{
					$this->$property = $value;
				}

			}
		}
	}
	
	public function getToken ($code,$refresh=null)
	{

		$params = [
		'type'=>'web_server',
		'client_id'=>$this->client_id,
		'redirect_uri'=>$this->redirect_uri,
		'client_secret'=>$this->client_secret,
		'code'=>$code
		];

		$token = $this->postData($this->token_url,$params);
		$this->token = $token;
		return $this;
	}

	/**
	 * Get Authorization
	 */
	public function getAuthorization()
	{
		$endpoint="https://launchpad.37signals.com/authorization.json";

		$authorized= $this->getData($endpoint,$this->token);
		foreach ($authorized['accounts'] as $account) {
			if($account['product'] =='bcx'){
				$this->accounts[] = $account;
			}
		}		
		$this->identity = $authorized['identity'];
		return $this;

	}
	

	public function getMe()
	{
		$endpoint = "people/me.json";
		$url="{$this->accounts[0]['href']}/{$endpoint}"; 
		$whoami = $this->getData($url,$this->token);
		$avatar = parse_url($whoami['avatar_url']);

		return [
		'name'=>$whoami['name'],
		'firstName'=>$this->identity['first_name'],
		'lastName'=>$this->identity['last_name'],
		'email'=>$whoami['email_address'],
		'avatar'=>"//{$avatar['host']}/{$avatar['path']}?{$avatar['query']}"
		];
	}

	public function getAccounts()
	{

		$accounts=array_column($this->accounts,'name');
		return ['basecamp'=>$accounts];
	}

	public function getProjects()
	{
		$endpoint="projects.json";
		$projects=array();
		$account_urls = array_column($this->accounts,'href'); 
		$index=0;
		foreach ($account_urls as $url) {
			$account_projects= $this->getData("{$url}/{$endpoint}");		
			if(!empty($account_projects)){
				foreach($account_projects as $project){
					$names[$index] = $project['name'];
					$index++;
					$projects[] = $project;
				}
			}
		}
		
		if(!empty($projects)){
			array_multisort($names,SORT_ASC,$projects);
		}		
		
		return $projects;
	}


	private function getAuthUrl($properties){
		$redirect_uri = urlencode($properties['redirect_uri']);
		return "{$properties['auth_url']}?client_id={$properties['client_id']}&redirect_uri={$redirect_uri}&type=web_server";
	}

	public function getTodos($todoLists){
		$todos = array();
		


		foreach($todoLists as $projectName => $lists){

			foreach ($lists as $list) {
				$todoList = $this->getData($list['url']);
				foreach ($todoList['todos']['remaining'] as $todo){				
					$todo['list'] = $list['name'];
					$todo['projectName'] = $projectName;
					$todo['siteUrl']=$this->getSiteUrl($todo['url']);
					$todos[]=$todo;
				}
			}
			
		}
		return $todos;
	}

	public function getTodoLists($profileProjects){		

		$todoLists=array();
		$list= array();
		foreach ($profileProjects as $project) {
			$endpoint = "{$project['id']}/todolists.json";
			$url = "{$project['url']}";
			$base= pathinfo($url,PATHINFO_DIRNAME);
			$todoLists[$project['name']] = $this->getData("{$base}/{$endpoint}");
		}
		
		return $todoLists;
	}

	protected function getSiteUrl($url){
		$points = ['/api/v1','.json'];
		$siteUrl = str_replace($points,'',$url);
		return $siteUrl;
	}

	private function getData($url){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		if(!empty($this->token)){
			curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$this->token['access_token']}"]);
		}		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data=curl_exec($ch);
		curl_close($ch);



		return json_decode($data,true);

	}
	private function postData($url,Array $params,$token=null){

		$params = http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		if(!empty($this->token)){
			curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$this->token['access_token']}"]);
		}	
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$data = curl_exec($ch);
		curl_close($ch);

		return json_decode($data,true);

	}
	public function getProperties()
	{
		$item = [
		"token"=>$this->token,
		"accounts"=>$this->accounts,
		"identity"=>$this->identity
		];
		return $item;
	}

	public function toArray(){
		$item = ["name"=>$this->name,"authUrl"=>$this->authUrl];
		return $item;
	}

	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
	
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}

}
