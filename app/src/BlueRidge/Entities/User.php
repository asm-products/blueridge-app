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
		if(empty($doc)){
			return;
		}
		$user = new User($doc);
		return $user->toArray();
	}
	public function fetchOneById($id){
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");	
		
		$doc=$users->findOne(array('_id' => new \MongoId($id)));
		if(empty($doc)){
			return;
		}

		$user = new User($doc);
		return $user->toArray();
	}

	public function create($authToken,$authUser){
		$db = $this->app->database;
		$users = new \MongoCollection($db,"Users");

		$authToken = json_decode($authToken);
		$authUser = json_decode($authUser);

		// check and see if the user already exists
		$doc = $this->fetchOne(["email"=>$authUser->identity->email_address]);

		if(empty($doc)){
			$doc = array(
				"email"=>$authUser->identity->email_address,
				"name"=>"{$authUser->identity->first_name} {$authUser->identity->last_name}",
				"services"=>array(
					"basecamp" =>array(
						"auth"=>$authToken,
						"user"=>$authUser
						)
					)
				);
			$users->insert($doc);
		}
		
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