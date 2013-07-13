<?php 
namespace BlueRidge;

abstract class ModelAbstract 
{	
	private $app;
	
	public function __construct($app,$properties=null){
		$this->app = $app;
		if(!empty($properties)){
			$this->setProperties($properties);
		}
	}
	protected function setProperties($properties){
		foreach($properties as $property => $value){
			if($property == "_id"){
				$this->id = (string) $value;
			}
			$this->$property = $value;
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