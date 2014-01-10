<?php
/**
 * Pull Todos
 * Fetch a users todos from providers
 */ 
namespace Blueridge\Jobs\Pull;

use Blueridge\Application;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;

class Todos 
{
    public function perform()
    {

        $blueridge= new Application();
        
        
        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->findOneById($this->args['userId']); 
        if(empty($user)){
            return;
        } 

        $projects = (!empty($this->args['projects']))?$this->args['projects']:null;
        $basecampClient = new Basecamp($blueridge);
        $todos = $basecampClient->getTodos($user,$projects);

        foreach($todos as $item)
        {
            $item['todoId']=$item['rel']['project']['account']['product'].'_'.$item['id'];
            unset($item['id']);

            $basecampClient = new Basecamp($blueridge);
            $item['source'] = $basecampClient->getTodo($user,$item['url']);


            // check for existing todo and update
            $todo = $todoQr->findOneByTodoId($item['todoId']);

            if(empty($todo))
            {
                $todo = new Todo();
            }

            $item  = $todo->polish($item);
            $todo->setProperties($item);        
            $blueridge['documentManager']->persist($todo);
            $blueridge['documentManager']->flush();
        }
        $total=count($todos);
        echo "updated {$total} todos";
    }

}