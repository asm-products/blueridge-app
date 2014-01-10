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
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Authentication Adapter
 */
class DocumentAdapter implements AdapterInterface
{
    /**
     * User email address
     * @var String 
     */
    private $email;

    /**
     * User password
     * @var string
     */
    private $password;

    /**
     * User document repository
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $userDocumentRepository;

    /**
     * Authentication Adapter
     * @param Doctrine\ODM\MongoDB\DocumentManager $documentManager 
     * @param string          $email          
     * @param string         $password        
     */
    public function __construct(DocumentManager $documentManager, $email, $password)
    {
        $this->userDocumentRepository = $documentManager->getRepository('\Blueridge\Documents\User'); 
        $this->email = $email;
        $this->password = $password;

    }

    /**
     * Authenticate a user
     * @return Zend\Authentication\Result Result
     */
    public function authenticate()
    {
        $user = $this->userDocumentRepository->findOneByEmail($this->email);        

        if (empty($user)) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, [], ['No user exists with the credentials provided']);
        }

        if (!password_verify($this->password, $user->key)) {           
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, [], ['Wrong Credentials ']);
        } 

        return new Result(Result::SUCCESS, $user->identifier, []);        
        
    }

}