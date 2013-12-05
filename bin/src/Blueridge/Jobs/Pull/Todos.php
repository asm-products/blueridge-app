<?php
/**
 * Pull Todos
 * Fetch a users todos from providers
 */ 
namespace Blueridge\Jobs\Pull;

use Blueridge\Blueridge;
use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;

class Todos 
{
    public function perform()
    {

        $blueridge= new Blueridge();
        $basecampClient = new Basecamp($blueridge);
        
        $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');


        $user= $userQr->findOneById($this->args['userId']);        
        $todos = $basecampClient->getTodos($user);

        foreach($todos as $item)
        {
            $item['todoId']=$item['rel']['project']['account']['product'].'_'.$item['id'];
            unset($item['id']);
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

        }
        $blueridge['documentManager']->flush();
        
    }

}