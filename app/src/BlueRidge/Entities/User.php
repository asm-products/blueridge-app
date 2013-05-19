<?php
/**
 * User
 */

namespace BlueRidge\Entities;

class User extends EntityAbstract
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
			$user = new User($doc);
			$users[] = $user->toArray();
		}
		return $users;		
	}
	public function fetchOne(Array $params=null){		
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	

		$doc=$users->findOne($params);
		$user = new User($doc);
		return $user->toArray();
	}
	public function fetchOneById($id){
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	
		$doc=$users->findOne(array('_id' => new \MongoId($id)));
		$user = new User($doc);
		return $user->toArray();
	}

	public function createFromBasecamp($authToken,$auth){
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");

		$token = json_decode($authToken,true);
		$auth = json_decode($auth,true);

		$doc = array(
			"email"=>$auth['identity']['email_address'],
			"name"=>"{$auth['identity']['first_name']} {$auth['identity']['last_name']}",
			"services"=>array(
				"basecamp" =>array(
					"auth"=>$token,
					"user"=>$auth
					)
				)
			);
		$users->insert($doc);
		$user = new User($doc);
		return $user->toArray();

	}
	public function update(Array $criteria, Array $data){

		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");
		$data = array('$set' => $data);
		return $users->update($criteria,$data);	
	}
	protected function toArray(){
		$item = ["id"=>$this->id,"name"=>$this->name,"email"=>$this->email,"services"=>$this->services];
		return $item;
	}
}