<?php
/**
 * User
 */

namespace BlueRidge\Documents;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** 
 * @ODM\Document(collection="Users",repositoryClass="\BlueRidge\Documents\UserRepository") 
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
     * Set Subscription 
     */
    public function initNewSubscriber($service)
    {
        \Stripe::setApiKey($service['secret_key']);
        $customer = \Stripe_Customer::create(['description' => $this->name,'email' =>$this->email,'plan'=>'br-free']);

        $this->subscription=[
        'customer_id'=>$customer->id,
        'plan'=>['id'=>$customer->subscription->plan->id,'name'=>$customer->subscription->plan->name],
        'card'=>'',
        'status'=>$customer->subscription->status
        ];

        return $this;
    }
}