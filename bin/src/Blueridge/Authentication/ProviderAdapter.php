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
class ProviderAdapter implements AdapterInterface
{
    /**
     * User identifier
     * @var String 
     */
    private $identifier;

    /**
     * User document repository
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $userDocumentRepository;

    /**
     * Authentication Adapter
     * @param Doctrine\ODM\MongoDB\DocumentManager $documentManager 
     * @param string         $identifier
     */
    public function __construct(DocumentManager $documentManager, $identifier)
    {
        $this->userDocumentRepository = $documentManager->getRepository('\Blueridge\Documents\User'); 
        $this->identifier = $identifier;
    }

    /**
     * Authenticate a user
     * @return Zend\Authentication\Result Result
     */
    public function authenticate()
    {        
        return new Result(Result::SUCCESS, $this->identifier, []);        

    }

}