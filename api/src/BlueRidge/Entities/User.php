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
	protected $password;

	/**
	 * Accounts
	 * @var Array
	 */
	protected $accounts;

	/**
	 * Plan
	 * @var string
	 */
	protected $plan='free';


	/**
	 * Providers
	 * @var string
	 */
	protected $providers;

	public function fetch(Array $params=null){
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
	
	public function fetchOne(Array $params=null){

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
	public function fetchOneById($id){
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	
		
		$doc=(object)$users->findOne(array('_id' => new \MongoId($id)));
		if(empty($doc)){
			return;
		}

		return $this->setProperties($doc);

	}
	public function fetchSegment($segment){

		// todos only
			$todo = new ToDo($this->app);
			$todos = $todo->fetchUserTodos($this);
			return $todos;
	}

	public function create($properties){
	
		$properties['password']=hash_hmac("sha256", $this->getInitPassword(), $properties['email']);
		$result = $this->update(["email"=>$properties['email']],$properties);
		
		if(empty($result)){
			return;
		}
		if(isset($result['error'])){
			return $result;
		}

		return $this->setProperties($result);

	}
	public function update(Array $criteria, $doc, $single=false){
		if(key($criteria)=='id'){
			$criteria =['_id'=>new \MongoId($criteria['id'])];
		}

		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");
		$result = $users->findAndModify($criteria,['$set' => $doc],null,["new" => true,"upsert"=>true]);
		
		if($single === true){
			return $this->setProperties($result);
		}
		
		return $result;

	}
	public function toArray(){
		$item = ["id"=>$this->id,"name"=>$this->name,"email"=>$this->email,'password'=>$this->password,"avatar"=>$this->avatar,"url"=>$this->url,"accounts"=>$this->accounts,'plan'=>$this->plan];
		return $item;
	}

	
	


}