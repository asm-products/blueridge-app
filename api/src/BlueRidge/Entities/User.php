<?php
/**
 * User
 */

namespace BlueRidge\Entities;

use BlueRidge\Providers\BasecampApi;

class User extends \BlueRidge\ModelAbstract
{
	/**
	 * User Id
	 * @var string
	 */
	protected $id;
	/**
	 * Name
	 * @var string
	 */
	protected $name;

	/**
	 * First Name
	 * @var string
	 */
	protected $firstName;

	/**
	 * User Name
	 * @var string
	 */
	protected $lastName;

	/**
	 * User Email
	 * @var string
	 */
	protected $email;

	/**
	 * User Url
	 * @var string
	 */
	protected $url;

	/**
	 * Avatar
	 * @var string
	 */
	protected $avatar;

	/**
	 * Authorization Key
	 * @var string
	 */
	protected $key;

	/**
	 * Projects
	 * @var Array
	 */
	protected $profile;

	/**
	 * Projects
	 * @var Array
	 */
	protected $projects;



	/**
	 * Subscription
	 * @var array
	 */
	protected $subscription;


	/**
	 * Providers
	 * @var string
	 */
	protected $providers;


	/**
	 * Collection
	 * @var Object
	 */
	protected $collection;


	public function __construct($app,$properties=null)
	{
		parent::__construct($app,$properties);
		$this->collection = new \MongoCollection($this->app->database,"Users");	

	}

	public function fetch(Array $params=null)
	{
		$users = array();
		$cursor=$this->collection->find();
		foreach ($cursor as $doc) {
			$user = new User($this->app,$doc);
			$users[] = $user->toArray();
		}
		return $users;		
	}
	
	public function fetchOne(Array $params=null)
	{

		if (isset($params['id'])){
			return $this->fetchOneById($params['id']);
		}		

		$doc=$this->collection->findOne($params);
		if(empty($doc)){
			return;
		}

		return $this->setProperties($doc);
	}

	public function fetchOneById($id)
	{
		
		$doc=(object)$this->collection->findOne(array('_id' => new \MongoId($id)));
		if(empty($doc)){
			return;
		}

		return $this->setProperties($doc);

	}

	public function fetchSegment($segment)
	{
		$data = null;
		switch ($segment) {
			case 'todos':
			$todo = new Todo($this->app);
			$data = $todo->fetchUserTodos($this);
			break;
			case 'accounts':
			$data = $this->profile['accounts']['basecamp'];
			break;
			case 'projects':
			$data = $this->fetchProjects();
			break;		
		}
		return $data;
	}


	public function create($properties)
	{
		
		//$freshId = $properties['providers']['basecamp']['auth']->identity['id'];
		$exists = $this->collection->count(['email'=>$properties['email']]);
			
		if(!empty($exists)){
			return $this->refresh($properties);
		}

		

		try{
			$this->collection->insert($properties);
			return ['status'=>201, 'resource'=>$this->setProperties($properties)];

		}catch(\Exception $error){
			return ['status'=>500,'message'=>"User Creation failed"];
		}
		
		return $this->setProperties($properties);

	}

	public function refresh(Array $properties)
	{	
		

		try{
			$user = $this->collection->findOne(['email'=>$properties['email']]);
			
			if(!empty($user['profile']['projects'])){
				$properties['profile']['projects'] = $user['profile']['projects'];	
			}



			$this->collection->update(['_id'=>$user['_id']],['$set' => $properties]);
			$updated= $this->collection->findOne(['_id'=>$user['_id']]);

			return ['status'=>200, 'resource'=>$this->setProperties($updated)];



		}catch(\Exception $error){

			return ['status'=>500,'message'=>"User Update failed"];
		}

	}


	public function update($id,Array $properties)
	{

		try{
			list($segment,$subset) = each($properties);
			foreach ($subset as $key => $property) {
				$this->collection->update(['_id'=>new \MongoId($id)],['$set'=>["{$segment}.{$key}"=>$property]]);
			}
			$user = $this->collection->findOne(['_id' => new \MongoId($id)]);

			return ['status'=>204, 'message'=>""];

		}catch(\Exception $error){

			return ['status'=>500,'message'=>"Profile update failed"];
		}

	}

	public function toArray()
	{
		$item = [
		"id"=>$this->id,
		"name"=>$this->name,
		"fullname"=>['first'=>$this->firstName,'last'=>$this->lastName],
		"email"=>$this->email,
		"key"=>$this->key,
		"avatar"=>$this->avatar,
		"profile"=>$this->profile
		];
		

		return $item;
	}
	private function fetchProjects()
	{
		$items=array();

		foreach($this->projects as $key => $project){
			
			$selected = (in_array($project['id'], $this->profile['projects']))?true:false;
			$item=$project;
			$item['selected']=$selected;	
			unset(
				$item['url'],
				$item['archived'],
				$item['created_at'],
				$item['updated_at'],
				$item['last_event_at'],
				$item['starred']
				);

			$items[]=$item;
		}
		return $items;
	}
}