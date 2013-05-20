<?php
/**
 * ToDo
 */

namespace BlueRidge\Entities;

use BlueRidge\Services\Basecamp;

class ToDo extends EntityAbstract
{
	/**
	 * ToDo Id
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
	protected $dueDate;

	/**
	 * Overdue By
	 * @var Int
	 */
	protected $overDueBy =0;

	/**
	 * Content
	 * @var String
	 */
	protected $content;

	/**
	 * Owner
	 * @var Object
	 */
	protected $owner;

	/**
	 * Project
	 * @var String
	 */
	protected $project;

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
	 * Fetch
	 * @return Array
	 */
	public function fetch($userid){

		$todos = array();
		$user = new User();
		
		// fetch user by email
		$user = $user->init($this->app)->fetchOneById($userid);

		// fetch the todo from the service and store them in cache
		$basecamp = new Basecamp($this->app);
		$todo_items= $basecamp->getTodos($user);

		
		foreach($todo_items as $key => $item){
			$initPos = 0;
			$todo = new Todo($item);
			$todo->owner = $item->assignee->name;
			$todo->url = $item->siteUrl;
			if(!empty($item->due_on)){
				$initPos = 1;
				$dueOn= new \DateTime($item->due_on);
				$todo->dueDate=$dueOn->format('m/d/Y');
				$todo->overDueBy = (int) $this->getOverdueBy($dueOn);
			}

			// sort order
			$due_date[$key] = $todo->dueDate;
			$position[$key] = $initPos;
			$overdueBy[$key] = $todo->overDueBy;

			$todos[]=$todo;
		}

		array_multisort($overdueBy,SORT_DESC,$position,SORT_DESC,$due_date,SORT_ASC,$todos);
		return $todos;	
	}

	private function getOverdueBy($dueDate){
		$now = new \DateTime('now');

		if($dueDate > $now){
			return 0;
		}

		$interval = $dueDate->diff($now);
		return $interval->format('%a');

	}
	protected function toArray(){
		$item = [
		"id"=>$this->id,
		"dueDate"=>$this->dueDate,
		"content"=>$this->content,
		"owner"=>$this->assignee,
		"project"=>$this->project,
		"list"=>$this->list,
		"url"=>$this->url
		];
		return $item;
	}
}