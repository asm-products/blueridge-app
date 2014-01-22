<?php
/**
 * Todo Repository
 */
namespace Blueridge\Documents;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Blueridge\Documents\User;

class TodoRepository extends DocumentRepository
{

    public function fetchByTodoIds(Array $todoIds)
    {
        return $this->createQueryBuilder()
        ->eagerCursor(true)    
        ->field('todoId')->in($todoIds)
        ->getQuery()->execute();
    }
    
    public function fetchByUser(User $user)
    {        
        return $this->createQueryBuilder()
        ->field('rel.project.id')->in($user->profile['projects'])
        ->getQuery()->execute();
    }

    /**
     * Fetch all the todos in a project
     * @param  BlueridgeDocumentsUser $user      
     * @param  String                 
     * @return Object                            
     */
    public function fetchByProject(User $user, $projectId)
    {
        return $this->createQueryBuilder()
        ->eagerCursor(true)    
        ->field('rel.project.id')->equals($projectId)
        ->getQuery()->execute();
    }

}