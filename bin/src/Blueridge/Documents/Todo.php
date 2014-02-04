<?php
/**
 * Todo
 */
namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\DocumentRepository;

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
     * @ODM\Index
     */
    protected $todoId;

    /**
     * Title
     * @var string
     * @ODM\String
     * @ODM\AlsoLoad("content")
     */
    protected $title;

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
    protected $dueOn;

    /**
     * Due Date
     * @var string
     * @ODM\String
     */
    protected $dueDate;

    /**
     * Overdue By
     * @var int
     * @ODM\Int
     * @ODM\AlsoLoad("overdue_by")
     */
    protected $overdueBy;

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
     * Return an Array representation of the Todo object
     * @return Array
     */
    public function toArray()
    {

        $properties = ['id','todoId','title','assignee','due_on','due_date','overdue_by','source','rel'];

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
     * Return an preped array for the view
     * @return Array
     */
    public function toView()
    {
        return [
        'id' => $this->id,
        'uid' => $this->todoId,
        'title' => $this->title,
        'assignee' => $this->assignee['name'],
        'due_on' => $this->dueOn,
        'overdue_by' => $this->overdueBy,
        'due_date' => $this->dueDate,
        'account_name' => $this->rel['project']['account']['name'],
        'project_name' => $this->rel['project']['name'],
        'todo_list' => $this->rel['list_name'],
        'href'=>$this->rel['href']
        ];
    }

    /**
     * Returns the Array Object with the expected properties for export
     * @return Array
     */
    public function toExport()
    {
        return [
        $this->source['due_on'],
        $this->overdue_by,
        $this->title,
        $this->rel['list_name'],
        $this->rel['project']['name'],
        $this->assignee['name'],
        $this->rel['href']
        ];
    }

    /**
     * Get Property
     * @param  mixed $property
     * @return mixed  $value
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {

            return $this->$property;
        }
    }

    /**
     * Set property
     * @param mixed $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
}