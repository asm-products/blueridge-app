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
	 * ToDo Item
	 * Service item object
	 * @var Object
	 */
	protected $item;

	/**
	 * Source
	 * The service that is supplying the todo
	 * @var String
	 */
	protected $source;

	/**
	 * Fetch
	 * @return Array
	 */
	public function fetch($params){

		$todos = array();
		$user = new User();
		
		// fetch user by email
		$user = $user->init($this->app)->fetchOneById($params['user']);

		// fetch the todo from the service and store them in cache
		$basecamp = new Basecamp($this->app);
		$todos= $basecamp->getTodos($user);
		return $todos;	
	}
	protected function toArray(){
		$item = ["id"=>$this->id,"item"=>$this->item,"service"=>$this->source];
		return $item;
	}
}