#!/usr/bin/php
<?php
/**
 * Blueridge
 * Sync All
 */

require realpath(dirname(__FILE__).'/../bootstrap.php');

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;


$userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
$todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

$basecampClient = new Basecamp($blueridge);
$raw_todos = $basecampClient->getTodos($user);
// $todoIds = array();

foreach($raw_todos as $item)
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

// log this job
var_dump(count($raw_todos));


// var_dump($todoUrls);