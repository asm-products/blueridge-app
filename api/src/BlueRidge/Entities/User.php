<?php
/**
 * User
 */

namespace BlueRidge\Entities;

use BlueRidge\ModelAbstract;
use BlueRidge\Providers\BasecampApi;
use BlueRidge\Utilities\Doorman;

class User extends ModelAbstract
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
	 * @var Array
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
			case 'subscription':
			$data = $this->fetchSubscription();
			break;		
		}
		return $data;
	}

	public function create($provider)
	{
		$properties = $this->prep($provider);
		$exists = $this->collection->count(['email'=>$properties['email']]);

		if(!empty($exists)){

			//return $this->refresh($properties);
		}

		$access = Doorman::Init();

		try{
			$properties['key']=$access['key'];
			$this->collection->insert($properties);
			$user = $this->setProperties($properties);
			return ['status'=>201, 'resource'=>$user,'access'=>$access];

		}catch(\Exception $error){
			return ['status'=>500,'message'=>"User Creation failed"];
		}

	}

	public function refresh(Array $properties)
	{	
		

		try{
			$user = $this->collection->findOne(['email'=>$properties['email']]);
			
			if(!empty($user['profile']['projects'])){
				$properties['profile']['projects'] = $user['profile']['projects'];	
			}
			if(!empty($user['subscription'])){
				$properties['subscription'] = $user['subscription'];	
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
		list($segment,$subset) = each($properties);
		switch($segment){
			case 'profile';
			$result = $this->updateProfile($id,$subset);
			break;
			case 'subscription':
			$result = $this->updateSubscription($id,$subset);
			break;
		}
		return $result;

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

	private function prep($provider)
	{
		$provider->getAuthorization();
		$properties = $provider->getMe();
		$properties['key']=(!empty($this->key))?$this->key:''; 
		$properties['providers'][$provider->name]=$provider->getProperties();
		$properties['projects']= $provider->getProjects();
		$properties['profile']['accounts']=$provider->getAccounts();
		$properties['profile']['projects']= (!empty($this->profile->projects))?$this->profile->projects:[];

		if(empty($this->subscription)){
			\Stripe::setApiKey($this->app->subscriber->secret_key);
			$customer = \Stripe_Customer::create(["description" => $properties['name'],"email" =>$properties['email'],'plan'=>'br-free']);

			$properties['subscription']=[
			'customer_id'=>$customer->id,
			'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
			'cards'=>$customer->cards->data,
			'status'=>$customer->subscription->status
			];
		}
		
		return $properties;
	}

	/**
	 * Fetch Projects
	 */
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
	private function updateProfile($id,$profile)
	{
		try{
			
			foreach ($profile as $key => $property) {
				$this->collection->update(['_id'=>new \MongoId($id)],['$set'=>["profile.{$key}"=>$property]]);
			}
			$user = $this->collection->findOne(['_id' => new \MongoId($id)]);

			return ['status'=>204, 'message'=>""];

		}catch(\Exception $error){

			return ['status'=>500,'message'=>"Profile update failed"];
		}

	}

	
	/**
	 * Fetch Subscription
	 */
	private function fetchSubscription()
	{
		$plan = $this->subscription['plan'];
		$payment = (Object) $this->subscription['payment'];
		return ['plan'=>$plan,'payment'=>$payment,'customer_id'=>$this->subscription['customer_id']];
	}


	/**
	 * Update subsription
	 */
	private function updateSubscription($id,$subscription)
	{
		//set the ammount 
		switch($subscription['plan']){
			case 'solo';
			$amount=795;
			break;
			case 'pro';
			$amount=1495;
			break;
		}

		$user = $this->collection->findOne(['_id' => new \MongoId($id)]);

		try{
			\Stripe::setApiKey($this->app->cashier->secret_key);

			if(empty($user['subscription']['customer_id'])){
				
				$customer = \Stripe_Customer::create(array(
					"card" => $subscription['payment']['id'],
					"plan" => $subscription['plan'],
					"email" =>$user['email'])
				);

				$user['subscription']=[
				'customer_id'=>$customer->id,
				'plan'=>$subscription['plan'],
				'payment'=>[
				'card'=>$subscription['payment']['card']]
				];
			}

			\Stripe_Charge::create(array(
				"amount" => $amount,
				"currency" => "usd",
				"customer" => $user['subscription']['customer_id'])
			);

			$this->collection->update(['_id'=>new \MongoId($id)],['$set'=>["subscription"=>$user['subscription']]]);
			return ['status'=>204, 'message'=>"Subscription updated successfully "];


		}catch(\Error $e){

			return ['status'=>500,'message'=>"Subscription update failed"];
			error_log($e->getMessage());
		}


	}
}