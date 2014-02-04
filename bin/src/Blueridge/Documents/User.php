<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
     * IdentifierKey
     * @var string
     * @ODM\String
     */
    protected $identifierKey;

    /**
     * Email
     * @var string
     * @ODM\String
     */
    protected $email;

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
    protected $roles;

    /**
     * Status
     * @var String
     * @ODM\String
     */
    protected $status;

    /**
     * Profile
     * @var Array
     * @ODM\Hash
     */
    protected $profile;

    /**
     * Projects
     * @var Array
     * @ODM\Hash
     */
    protected $projects;

    /**
     * Subscription
     * @var Array
     * @ODM\Hash
     */
    protected $subscription;

    /**
     * Providers
     * @var Array
     * @ODM\Hash
     */
    protected $providers;

    /**
     * Member Since
     * @var Date
     * @ODM\Date
     */
    protected $member_since;


    /**
     * Set Properties
     * @param Array $properties
     * @return Object User
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
     * Update the profile
     * @param  string $segment
     * @param  Array $properties
     * @return boolean
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
        $properties = ['id','name','firstName','lastName','email','avatar','status','subscription'];

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
     * Returns an array of the users selected projects
     * @return Array Projects
     */
    protected function fetchProjects()
    {
        $projects=array();

        foreach($this->projects as $key => $project){

            $selected = (in_array($project['id'], $this->profile['projects']))?true:false;
            $item=$project;
            $item['selected']=$selected;
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