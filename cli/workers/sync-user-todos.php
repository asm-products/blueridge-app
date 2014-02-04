#!/usr/bin/php
<?php
/**
 * Blueridge
 * Sync Todos
 */

require realpath(dirname(__FILE__).'/../init.php');

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;
use Blueridge\Providers\Basecamp;
use Blueridge\Application;



// get the user id
$options = getopt("u:");
if(empty($options['u']))
{

    try {
        throw new Exception("Error Processing Request: User Id missing", 1);
    } catch (Exception $e) {
        error_log($e->getMessage()."\n");
    }
    exit(1);    
}

$blueridge = new Blueridge();


$id = $options['u'];
$user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $id);
$todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

$basecampClient = new Basecamp($blueridge);
$todos = $basecampClient->getTodos($user);

// $todoIds = array();

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

// log this job
var_dump(count($todos));


// var_dump($todoUrls);