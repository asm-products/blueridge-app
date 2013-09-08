<?php
/**
 * Basecamp Auth classes
 */

namespace BlueRidge\Providers\Basecamp;

use \BlueRidge\Documents\User;
use \Guzzle\Common\Event;

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
	protected $handler;

	public function __construct($settings)
	{		
		$this->setProperties($settings);
	}

	public static function factory($app)
	{
		$properties = $app->config('providers')['basecamp'];
		$handler = $app->provider;
		$handler->setUserAgent($properties['user_agent']);		
		$properties['handler'] = $handler;
		$client = new self($properties);

		return $client;
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
	
	/**
	 * Get Token
	 */
	public function getToken ($code,$refresh=null)
	{

		$params = [
		'type'=>'web_server',
		'client_id'=>$this->client_id,
		'redirect_uri'=>$this->redirect_uri,
		'client_secret'=>$this->client_secret,
		'code'=>$code
		];

		$request = $this->handler->post($this->token_url,[],$params);
		$response = $request->send();
		$this->token = $response->json();
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
	
	/**
	 * Get Me
	 */
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

	/**
	 * Get Accounts
	 */
	public function getAccounts()
	{

		$accounts=array_column($this->accounts,'name');
		return ['basecamp'=>$accounts];
	}

	/**
	 * Get Projects
	 */
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

	/**
	 * Get Auth Url
	 */
	private function getAuthUrl($properties){
		$redirect_uri = urlencode($properties['redirect_uri']);
		return "{$properties['auth_url']}?client_id={$properties['client_id']}&redirect_uri={$redirect_uri}&type=web_server";
	}

	/**
	 * Get Profile Projects
	 */
	public function getProfileProjects(User $user)
	{

		if(empty($user->profile['projects'])){
			return;
		}		

		// set up profile projects
		$projectIter = new \ArrayIterator($user->projects);
		$profileProjects = array();

		foreach($projectIter as $project){
			if(in_array($project['id'], $user->profile['projects']))
			{
				$base= pathinfo($project['url'],PATHINFO_DIRNAME);
				//list($sp,$accountId)=explode('/',parse_url($project['url'])['path']);								
				$profileProjects[] = ['baseUrl'=>$base,'projectId'=>$project['id'],'name'=>$project['name']];

			}
		}

		return $profileProjects;

	}

	/**
	 * Get Todos
	 */
	public function getTodos(User $user)
	{
		// get profile projects
		$profileProjects = $this->getProfileProjects($user);

		// get todo lists
		$todolists = $this->getTodoLists($user, $profileProjects);
		
		// get the todos
		$todos = [];
		$todolistIterator = new \RecursiveArrayIterator($todolists);
		foreach (new \RecursiveArrayIterator($todolistIterator)  as $list) {

			$request = $this->handler->get($list['url']);
			$response = $request->send();
			$data = $response->json();
			$incomplete_todos = $data['todos']['remaining'];			
			$inherited = ['project'=>$list['project'],'list'=>$list['name']];

			array_walk($incomplete_todos, function(&$a, $key, $inherited) {				
				$a['project'] = $inherited['project'];
				$a['list'] = $inherited['list'];
				$a['href'] = BasecampClientHelper::getSiteUrl($a['url']);				
			},$inherited);

			$todos = array_merge($todos,$incomplete_todos);
		}
		return BasecampClientHelper::organizeTodos($todos);
	}

	/**
	 * Get Todolists
	 */
	public function getTodoLists(User $user, $profileProjects){	

		$projectsIterator = new \ArrayIterator($profileProjects);

		$todoLists=array();
		$list= array();
		foreach ($projectsIterator as $project) {
			$project_name = $project['name'];
			$endpoint = "{$project['baseUrl']}/{$project['projectId']}/todolists.json";
			$request = $this->handler->get($endpoint);
			$response = $request->send();
			$todolist = $response->json();

			array_walk($todolist, function(&$a, $key, $project_name) {				
				$a['project'] = $project_name;
			},$project_name);

			$todoLists=array_merge($todoLists, $todolist);
		}
		
		return $todoLists;
	}

	/**
	 * Set User Authorization
	 */
	public function setAuth(User $user)
	{
		
		$settings = $user->providers['basecamp'];
		$this->setProperties($settings);

		$authorization ="Bearer {$settings['token']['access_token']}";			
		$this->handler->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($authorization) {
			$event['request']->addHeader('Authorization', $authorization);

		});
		return $this;
	}

	/**
	 * Get Data
	 * @deprecated
	 */
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

	/**
	 * Post Data
	 * Post data to basecamp
	 * @deprecated
	 */
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
