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
     * Content
     * @var string
     * @ODM\String
     */
    protected $content;


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
     * Due Date
     * @var timestamp
     * @ODM\Timestamp
     */
    protected $due_date;

    /**
     * Overdue By
     * @var int
     * @ODM\Int
     */
    protected $overdue_by;
    

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
        // $this->assignee = new \ArrayCollection();
        // $this->source = new \ArrayCollection;
        // $this->rel = new \ArrayCollection;
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

        $properties = ['id','todoId','content','assignee','due_on','due_date','overdue_by','rel'];

        $item=array();

        foreach ($this as $property => $value)
        {
            if (in_array($property, $properties))
            {               
                $item[$property]=$value;                                
            }
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