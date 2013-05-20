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