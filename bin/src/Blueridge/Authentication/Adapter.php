<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Authentication Adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * User email address
     * @var String 
     */
    private $email;

    /**
     * User password
     * @var String
     */
    private $password;


    /**
     * User Query Object
     * @var Object
     */
    protected $userDocumentRepository;

    
    public function __construct(DocumentRepository $userDocumentRepository, $email, $password)
    {
        $this->userDocumentRepository = $userDocumentRepository;
        $this->email = $email;
        $this->password = $password;

    }

    public function authenticate()
    {
        $user = $this->userDocumentRepository->findOneByEmail($this->email);

        if (empty($user)) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, [], ['No user exists with the credentials provided']);
        }

        if (!password_verify($this->password, $user->key)) {           
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, [], ['Wrong Credentials ']);
        } 

        return new Result(Result::SUCCESS, $user->email, []);        
        
    }

}