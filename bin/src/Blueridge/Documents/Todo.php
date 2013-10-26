<?php
/**
 * Todo
 */
namespace Blueridge\Documents;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\ODM\MongoDB\DocumentRepository;

/** 
 * @ODM\Document(collection="Todos") 
 */
class Todo
{
    /**
     * Todo Id
     * @var string
     * @ODM\Id
     */

    protected $id;
    /**
     * Assignee
     * @var string
     * @ODM\String   
     */
    protected $assignee;

    /**
     * Due On
     * @var string
     * @ODM\Timestamp
     */
    protected $dueOn;

    /**
     * Overdue By
     * @var string
     * @ODM\String
     */
    protected $overdueBy;
    
    /**
     * Href
     * @var string
     * @ODM\String
     */
    protected $href;
    

    /**
     * Source
     * @var Array
     * @ODM\Hash
     */
    protected $source;

    /**
     * Parent
     * @var Array
     * @ODM\Hash
     */
    protected $parent;

    /**
     * Users
     * @var Array
     * @ODM\Hash
     */
    protected $users;


    /**
     * Set Properties
     * @return Object
     */ 
    public function setProperties(Array $properties)
    {
        foreach($properties as $property => $value){          
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        return $this;
    }
    

    /**
     * To Array
     * @return Array
     */
    public function toArray()
    {

        $item=array();

        foreach ($this as $property => $value)
        {            
            $item[$property]=$value;                                
        }
        return $item;
    }

    /**
     * Getter
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {

            return $this->$property;   
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