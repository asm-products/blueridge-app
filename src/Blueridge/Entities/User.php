<?php
/**
 * User
 */

namespace BlueRidge\Entities;

use BlueRidge\Providers\BasecampApi;
use BlueRidge\ModelAbstract;

class User
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

	/**
	 * App
	 * @var $app
	 */
	private $app;


	public function __construct($app,$properties=null)
	{
		$this->app = $app;
		$this->collection = new \MongoCollection($app->db,"Users");	

		if(!empty($properties)){

			foreach($properties as $property => $value){
				if($property == "_id"){
					$this->id = (string) $value;
				}
				$this->$property = $value;
			}
		}

	}


	public function search(Array $params=null)
	{
		$users = array();
		$cursor=$this->collection->find();
		foreach ($cursor as $doc) {
			$user = new User($this->app,$doc);
			$users[] = $user->toArray();
		}
		return $users;		
	}
	
	public function fetch(Array $params=null)
	{	

		$doc=$this->collection->findOne($params);
		if(empty($doc)){
			return;
		}
		return $this->setProperties($doc);
	}

	/**
	 * Fetch One By Id
	 */
	public function fetchOne($id)
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
			$data['todos'] = $todo->fetchUserTodos($this);
			break;
			case 'accounts':
			$data['accounts'] = $this->profile['accounts']['basecamp'];
			break;
			case 'projects':
			$data['projects'] = $this->fetchProjects();
			break;
			case 'subscription':
			$data = $this->fetchSubscription();
			break;		
		}
		return $data;
	}
	public function exists(Array $params)
	{
		return $this->collection->count($params);
	}

	/**
	 * Create
	 * @deprecated
	 */
	public function create($provider)
	{
		$properties = $this->prep($provider);
		$exists = $this->collection->count(['email'=>$properties['email']]);

		/*
		if(!empty($exists)){

			return $this->refresh($properties);
		}*/

		try{			
			$this->collection->insert($properties);
			$user = $this->setProperties($properties);
			return ['user'=>$user,'access'=>$access];

		}catch(\Exception $error){
			return ['message'=>"User Creation failed"];
		}

	}

	/**
	 * Refresh User
	 * @deprecated
	 */
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

	/**
	 * Update
	 */
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

	/**
	 * To Array
	 */
	protected function toArray()
	{
		
		$properties = ['id','name','firstName','lastName','email','key','url','avatar','profile','projects','subscription'];

		$document=array();

		foreach ($this as $property => $value)
		{
			if (in_array($property, $properties))
			{
				if($property == 'id'){
					$document['_id'] = new \MongoId($value);
				}else{
					$document[$property]=$value;
				}
				
			}

		}


		return $document;
	}

	/**
	 * Set Properties
	 * @return Object
	 */	
	protected function setProperties($document)
	{
		foreach($document as $property => $value){
			if($property == "_id"){
				$this->id = (string) $value;
			}
			$this->$property = $value;
		}
		return $this;
	}

	/**
	 * Save
	 * @return Object
	 */
	public function save()
	{

		$document= $this->toArray();
		$this->collection->insert($document,['w'=>true]);
		return $this;
	}


	/**
	 * Set Subscription 
	 */
	public function setSubscription()
	{
		\Stripe::setApiKey($this->app->config('services')['subscriber']['secret_key']);
		$customer = \Stripe_Customer::create(['description' => $this->name,'email' =>$this->email,'plan'=>'br-free']);

		$this->subscription=[
		'customer_id'=>$customer->id,
		'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
		'card'=>'',
		'status'=>$customer->subscription->status
		];

		return $this;
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

			\Stripe::setApiKey($this->app->config('services')['subscriber']['secret_key']);
			$customer = \Stripe_Customer::create(["description" => $properties['name'],"email" =>$properties['email'],'plan'=>'br-free']);

			$properties['subscription']=[
			'customer_id'=>$customer->id,
			'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
			'card'=>'',
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
		$projects=array();

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

			$projects[]=$item;
		}
		return $projects;
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
		//unset($this->subscription['card']['id']);
		return [
		'plan'=>$this->subscription['plan'],
		'card'=>$this->subscription['card'],
		'status'=>$this->subscription['status']
		];
	}


	/**
	 * Update subscription
	 */
	private function updateSubscription($id,$subscription)
	{

		$user = $this->collection->findOne(['_id' => new \MongoId($id)]);

		if($subscription['plan']=='br-free'){
			$subscription['payment']=null;
		}


		try{
			\Stripe::setApiKey($this->app->config('services')['subscriber']['secret_key']);
			$customer = \Stripe_Customer::retrieve($user['subscription']['customer_id']);
			$customer->updateSubscription(array("plan" => $subscription['plan'], "prorate" => true,'card'=>$subscription['payment']));

			$cards = $customer->cards->all(array('count'=>1));

			$card = [
			'id'=>$cards['data'][0]['id'],
			'last4'=>$cards['data'][0]['last4'],
			'exp_month'=>$cards['data'][0]['exp_month'],
			'exp_year'=>$cards['data'][0]['exp_year'],
			'type'=>$cards['data'][0]['type'],
			];
			


			$plan=['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name];

			$updated = ($subscription['plan']=='br-free')? ["subscription.plan"=>$plan]:["subscription.plan"=>$plan,"subscription.card"=>$card];
			

			$this->collection->update(['_id'=>new \MongoId($id)],['$set'=>$updated]);

			return ['status'=>204, 'message'=>"Subscription updated successfully "];


		}catch(\Error $e){

			return ['status'=>500,'message'=>"Subscription update failed"];
			error_log($e->getMessage());
		}


	}

	/**
	 * Getter
	 */
	public function __get($property)
	{
		if (property_exists($this, $property)) {

			switch($property)
			{
				case 'accounts':
				$data= $this->profile['accounts']['basecamp'];
				break;
				case 'projects':
				$data = $this->fetchProjects();
				break;
				case 'subscription':
				$data = $this->fetchSubscription();
				break;
				default:
				$data = $this->$property;

			}		
			return $data;			
		}
	}
	
	/**
	 * Setter
	 */
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
}