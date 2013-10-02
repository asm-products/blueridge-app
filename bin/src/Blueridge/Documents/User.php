<?php
/**
 * User
 */

namespace Blueridge\Documents;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\ODM\MongoDB\DocumentRepository;

/** 
 * @ODM\Document(collection="Users",repositoryClass="Blueridge\Documents\UserRepository") 
 */
class User
{
    /**
     * User Id
     * @var string
     * @ODM\Id
     */

    protected $id;
    /**
     * Name
     * @var string
     * @ODM\String   
     */
    protected $name;

    /**
     * First Name
     * @var string
     * @ODM\String
     */
    protected $firstName;

    /**
     * Last Name
     * @var string
     * @ODM\String
     */
    protected $lastName;

    /**
     * Email
     * @var string
     * @ODM\String
     */
    protected $email;

    /**
     * Url
     * @var string
     * @ODM\String
     */
    protected $url;

    /**
     * Avatar
     * @var string
     * @ODM\String
     */
    protected $avatar;

    /**
     * Key
     * @var string
     * @ODM\String
     */
    protected $key;

    /**
     * Roles
     * @var Array
     * @ODM\Hash
     */
    protected $roles = array();

    /**
     * Profile
     * @var Array
     * @ODM\Hash
     */
    protected $profile = array();

    /**
     * Projects
     * @var Array
     * @ODM\Hash
     */
    protected $projects = array();

    /**
     * Subscription
     * @var Array
     * @ODM\Hash
     */
    protected $subscription = array();

    /**
     * Providers
     * @var Array
     * @ODM\Hash
     */
    protected $providers;

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
     * Update Profile
     */
    public function updateProfile($segment,$properties)
    {
        try{
            $this->profile[$segment]= $properties;
            return true;
        }catch(\Exception $error){
            \error_log($error->getMessage());
            return;
        }
    }

    /**
     * To Array
     * @return Array
     */
    public function toArray()
    {
        $properties = ['name','firstName','lastName','email','url','avatar','subscription'];

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
     * Fetch Projects
     */
    protected function fetchProjects()
    {
        $projects=array();

        foreach($this->projects as $key => $project){
            
            $selected = (in_array($project['id'], $this->profile['projects']))?true:false;
            $item=$project;
            $item['selected']=$selected;    
            unset(              
                $item['archived'],
                $item['created_at'],
                $item['updated_at'],
                $item['last_event_at'],
                $item['starred']
                );

            $projects[]=$item;
        }
        return $projects;
    }

    /**
     * Getter
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {

            switch($property)
            {
                case 'accounts':
                $data= $this->profile['accounts']['basecamp'];
                break;
                case 'projects':
                $data = $this->fetchProjects();
                break;
                default:
                $data = $this->$property;

            }       
            return $data;           
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