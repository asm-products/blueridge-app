<?php
/**
 * Todo
 */

namespace BlueRidge\Entities;

use \BlueRidge\Providers\BasecampClient;
use \BlueRidge\Documents\User;

class Todo
{
	/**
	 * Todo Id
	 * @var String
	 */
	protected $id;

	/**
	 * Position
	 * @var String
	 */
	protected $position;

	/**
	 * Date
	 * @var String
	 */
	protected $dueDate='';

	/**
	 * Overdue By
	 * @var Int
	 */
	protected $overDueBy =0;

	/**
	 * Content
	 * @var String
	 */
	protected $content='';

	/**
	 * Owner
	 * @var Object
	 */
	protected $owner='';

	/**
	 * Project Name
	 * @var String
	 */
	protected $projectName='';

	/**
	 * List
	 * @var String
	 */
	protected $list;

	/**
	 * Url
	 * @var String
	 */
	protected $url;

	/**
	 * App
	 * @var Object
	 */
	private $app;
	
	/**
	 * Constructor
	 */
	public function __construct($app,$properties=null)
	{
		$this->app = $app;		
		if(!empty($properties)){

			foreach($properties as $property => $value){
				if($property == "_id"){
					$this->id = (string) $value;
				}
				$this->$property = $value;
			}
		}
	}	


	/**
	 * Fetch
	 * @return Array
	 * 
	 */
	public function fetch($params)
	{

		// loop through params 
		if(isset($params['user'])){
			$id = $params['user'];
		}else{
			//need to return with a 404;
			return ;
		}

		$fresh = isset($params['fresh'])?$params['fresh']:false;

		// we are only doing users and ids right now
		$user = new User($this->app);
		$user->fetchOneById($id);
		if(!$user->id){
			return;
		}

		return $this->fetchUserTodos($user);
		
	}
	
	public function fetchByUser(User $user)
	{
		// no project selected
		if(empty($user->profile['projects'])){
			return;
		}

		foreach($user->projects as $project){
			if(in_array($project['id'], $user->profile['projects'])){
				$profileProjects[] = $project;
			}
		}

		// project mismatch with basecamp
		if(empty($profileProjects)){
			return;
		}

		$settings = array_merge($this->app->config('providers')['basecamp'],$user->providers['basecamp']);

		$basecampClient = new BasecampClient($settings);

		$todoLists=$basecampClient->getTodoLists($profileProjects);
		$todos = $basecampClient->getTodos($todoLists);

		return $this->organize($todos);

	}

	private function organize($todoItems)
	{

		foreach($todoItems as $key => $item){
			$initPos = 0;
			$todo = new Todo($this->app,$item);

			if(!empty($item['assignee'])){
				$todo->owner = $item['assignee'];
			}	
			$todo->url = $item['siteUrl'];
			if(!empty($item['due_on'])){
				$initPos = 1;
				$dueOn= new \DateTime($item['due_on']);
				$todo->dueDate=$dueOn->format('m/d/Y');
				$todo->overDueBy = (int) $this->getOverdueBy($dueOn);
			}

			// set initial order
			$due_date[$key] = $todo->dueDate;
			$position[$key] = $initPos;
			$overdueBy[$key] = $todo->overDueBy;

			$todos[]=$todo->toArray();
		}

		array_multisort($overdueBy,SORT_DESC,$position,SORT_DESC,$due_date,SORT_ASC,$todos);
		return $todos;	

	}

	private function getOverdueBy($dueDate)
	{
		$now = new \DateTime('now');

		if($dueDate > $now){
			return 0;
		}

		$interval = $dueDate->diff($now);
		return $interval->format('%a');

	}

	protected function toArray()
	{
		$item = [
		"id"=>$this->id,
		"dueDate"=>$this->dueDate,
		"content"=>$this->content,
		"owner"=>$this->owner,
		"list"=>$this->list,
		"url"=>$this->url,
		"projectName"=>$this->projectName,
		"overDueBy"=>$this->overDueBy
		];
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