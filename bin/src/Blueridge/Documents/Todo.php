<?php
/**
 * Todo
 */
namespace Blueridge\Documents;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\ODM\MongoDB\DocumentRepository;

/** 
 * @ODM\Document(collection="Todos",repositoryClass="Blueridge\Documents\TodoRepository") 
 */
class Todo
{
    /**
     * Id
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * TodoId
     * @var string
     * @ODM\String
     */
    protected $todoId;


    /**
     * Assignee
     * @var Array
     * @ODM\Hash   
     */
    protected $assignee;

    /**
     * Due On
     * @var string
     * @ODM\String
     */
    protected $due_on;

    /**
     * Overdue By
     * @var string
     * @ODM\String
     */
    protected $overdue_by;
    
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
     * Rel
     * @var Array
     * @ODM\Hash
     */
    protected $rel;

    /**
     * Todo
     */
    public function __construct()
    {
        $this->assignee = new ArrayCollection;
        $this->source = new ArrayCollection;
        $this->rel = new ArrayCollection;
    }



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

    /**
     * Polish
     */
    public function polish($todo)
    {

        $todo['overdue_by'] = 0;
        $now= new \DateTime('now');

        if(!empty($todo['due_on'])){            
            $due_on= new \DateTime($todo['due_on']);                
            $todo['due_date']=$due_on->getTimestamp();
            if($now > $due_on){
                $todo['overdue_by']= $due_on->diff($now, true)->format('%a');
            }

        }else{                
            $todo['due_date']=$now->add(new \DateInterval('P6Y'))->getTimestamp();
        }

        if(empty($todo['assignee']))
        {
            $todo['assignee'] = ['id'=>null,'type'=>'Person','name'=>'Unassigned'];
        }

        return $todo;

    }
}