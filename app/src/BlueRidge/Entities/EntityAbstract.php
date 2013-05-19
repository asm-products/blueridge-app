<?php 
/**
 * EntityAbstract
 */
namespace BlueRidge\Entities;

abstract class EntityAbstract 
{	
	private $app;
	
	public function __construct($properties=null){

		if(!empty($properties)){
			foreach($properties as $property => $value){
				if($property == "_id"){
					$this->id = (string) $value;
				}
				$this->$property = $value;
			}
			return $this;
		}		
	}
	/**
	 * ToArray
	 * @return Array
	 * get aan array representation of the object
	 */
	/*
	protected function toArray(){
		$reflect = new \ReflectionClass($this);
		$properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
		$item = array();
		foreach($properties as $prop){
			
			$item[$prop->name] = $this->$prop->name;
			var_dump($item);
		}

		exit();

		$item = ["id"=>$this->id];


		return $item;
	}
	*/

	public function init($app){
		if (!empty($app)){
			$this->app = $app;
		}
		return $this;
	}

	public function __get($property){
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
}