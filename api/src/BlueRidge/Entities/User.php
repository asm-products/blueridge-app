<?php
/**
 * User
 */

namespace BlueRidge\Entities;

class User extends \BlueRidge\ModelAbstract
{
	/**
	 * User Id
	 * @var string
	 */
	protected $id;

	/**
	 * User Name
	 * @var string
	 */
	protected $name;

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
	 * Current Plan
	 * @var array
	 */
	protected $plan='free';

	/**
	 * Accounts
	 * @var Array
	 */
	protected $accounts;

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

	public function fetch(Array $params=null)
	{
		$users = array();
		$db = $this->app->database;
		$collection = new \MongoCollection($db,"Users");		
		$cursor=$collection->find();

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

		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	

		$doc=$users->findOne($params);
		if(empty($doc)){
			return;
		}

		return $this->setProperties($doc);
	}

	public function fetchOneById($id)
	{
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	
		
		$doc=(object)$users->findOne(array('_id' => new \MongoId($id)));
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
			$data = ['accounts'=>$this->accounts];
			break;
			case 'projects':
			$data = ['list'=>$this->projects,'selected'=>$this->selected_projects];
			break;		
		}
		return $data;
	}

	public function create($properties)
	{
		$users= new \MongoCollection($this->app->database,"Users");
		$users->insert($properties);
		return $this->setProperties($properties);

	}

	/**
	 * Refresh
	 * Refresh User Data
	 * This will always reset everything escept the keys
	 */
	public function refresh(Array $properties)
	{	

		$users = new \MongoCollection($this->app->database,"Users");
		$users->update(['_id'=>new \MongoId($this->id)],['$set' => $properties]);
		$user= $users->findOne();
		return $this->setProperties($user);

	}


	public function update(Array $properties)
	{
		
		$users = new \MongoCollection($this->app->database,"Users");
		$users->update(['_id'=>new \MongoId($this->id)],['$set' => $properties],["upsert"=>true]);
		$user = $users->findOne();
		return $this->setProperties($user);
	}

	public function toArray()
	{
		$item = [
		"id"=>$this->id,
		"name"=>$this->name,
		"email"=>$this->email,
		"key"=>$this->key,
		"avatar"=>$this->avatar,
		"plan"=>$this->plan
		];
		return $item;
	}
}