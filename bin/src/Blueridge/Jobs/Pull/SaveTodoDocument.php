<?php
/**
 * Pull Todo
 * Fetch a users todos from providers
 */ 
namespace Blueridge\Jobs\Pull;

use Blueridge\Application;
use Blueridge\Documents\Todo;

class SaveTodoDocument
{
    public function perform()
    {
        $todoItem = $this->args['todoItem'];
        $blueridge= new Application();
        $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');
        $todo = $todoQr->findOneBy(['todoId'=>$todoItem['todoId']]);

        if (empty($todo)) {
            $todo = new Todo();
            $todoItem  = $todo->polish($todoItem);
            $todo->setProperties($todoItem);        
            $blueridge['documentManager']->persist($todo);
            $blueridge['documentManager']->flush();
        }
        
        var_dump($todo);

    }

}