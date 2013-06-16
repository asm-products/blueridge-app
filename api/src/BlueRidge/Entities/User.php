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
	 * Projects
	 * @var string
	 */
	protected $projects;

	/**
	 * Services
	 * @var string
	 */
	protected $services;

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

	public function create($properties){
		
		$user = $this->update(["email"=>$properties['email']],$properties);
		return $this->setProperties($user);

	}
	public function update(Array $criteria, Array $doc){

		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");
		$result = $users->findAndModify($criteria,['$set' => $doc],null,["new" => true,"upsert"=>true]);
		return $result;

	}
	public function toArray(){
		$item = ["id"=>$this->id,"name"=>$this->name,"email"=>$this->email,"avatar"=>$this->avatar,"url"=>$this->url,"projects"=>$this->projects];
		return $item;
	}

}