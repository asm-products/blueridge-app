<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Providers;

class BasecampApi extends \BlueRidge\ModelAbstract
{

	protected $name;
	protected $userAgent;
	protected $client_id;
	protected $client_secret;
	protected $redirect_uri;
	protected $authUrl;
	protected $tokenUrl;

	public function __construct($app){
		parent::__construct($app);
		$this->setProperties($app->providers->basecamp);
		return $this;
	}

	protected function setProperties($properties){
		foreach($properties as $property => $value){
			if($property == "auth_url"){
				$this->authUrl = $this->getAuthUrl($properties);
			}
			if($property == "token_url"){
				$this->tokenUrl = $value;
			}			
			$this->$property = $value;
		}
		return $this;
	}
	
	public function authorize ($code){

		$params = [
		'type'=>'web_server',
		'client_id'=>$this->client_id,
		'redirect_uri'=>$this->redirect_uri,
		'client_secret'=>$this->client_secret,
		'code'=>$code
		];

		return $this->postData($this->tokenUrl,$params);
		
	}
	public function getAuthorization($token){
		$endpoint="https://launchpad.37signals.com/authorization.json";
		$authorized= $this->getData($endpoint,$token);
		$auth = new \stdClass();
		$auth->token=$token;

		foreach ($authorized['accounts'] as $account) {
			if($account['product'] =='bcx'){
				$auth->accounts[] = $account;
			}
		}
		
		$auth->identity = $authorized['identity'];
		return $auth;

	}

	public function getMe($auth,$token){
		$endpoint = "people/me.json";
		$url="{$auth->accounts[0]['href']}/{$endpoint}"; 
		$whoami = $this->getData($url,$token);

		return [
		'name'=>$whoami['name'],
		'email'=>$whoami['email_address'],
		'avatar'=>$whoami['avatar_url']
		];
	}

	public function getAccounts($auth,$token)
	{
		
		if(empty($auth)){
			$auth= $this->getAuthorization($token);
		}
		$accounts=array_column($auth->accounts,'name');
		return ['basecamp'=>$accounts];
	}

	public function getProjects($auth,$token)
	{
		$endpoint="projects.json";
		$projects=array();
		if(empty($auth)){
			$auth= $this->getAuthorization($token);
		}

		$account_urls = array_column($auth->accounts,'href'); 
		$index=0;
		foreach ($account_urls as $url) {
			$account_projects= $this->getData("{$url}/{$endpoint}",$token);		
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
		$redirect_uri = urlencode($properties->redirect_uri);
		return "{$properties->auth_url}?client_id={$properties->client_id}&redirect_uri={$redirect_uri}&type=web_server";
	}

	public function getTodos($todoLists,$token){
		$todos = array();
		


		foreach($todoLists as $projectName => $lists){

			foreach ($lists as $list) {
				$todoList = $this->getData($list['url'],$token);
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

	public function getTodoLists($profileProjects,$token){
		$todoLists=array();
		$list= array();
		foreach ($profileProjects as $project) {
			$endpoint = "{$project['id']}/todolists.json";
			$url = "{$project['url']}";
			$base= pathinfo($url,PATHINFO_DIRNAME);
			$todoLists[$project['name']] = $this->getData("{$base}/{$endpoint}",$token);
		}

		
		return $todoLists;
	}

	protected function getSiteUrl($url){
		$points = ['/api/v1','.json'];
		$siteUrl = str_replace($points,'',$url);
		return $siteUrl;
	}

	private function getData($url,$token){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		if(!empty($token)){
			curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$token['access_token']}"]);
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
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		if(!empty($token)){
			curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$token['access_token']}"]);
		}	
		curl_setopt($ch, CURLOPT_HEADER,0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$data = curl_exec($ch);
		curl_close($ch);

		return json_decode($data,true);

	}

	public function toArray(){
		$item = ["name"=>$this->name,"authUrl"=>$this->authUrl];
		return $item;
	}

}
