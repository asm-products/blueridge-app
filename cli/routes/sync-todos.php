#!/usr/bin/php
<?php
/**
 * Blueridge
 * Sync Todos
 */

require realpath(dirname(__FILE__).'/../bootstrap.php');

use Blueridge\Documents\User;
use Blueridge\Providers\Basecamp;

// var_dump($blueridge);
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

$id = $options['u'];
$user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $id);

$basecampClient = new Basecamp($blueridge);
$todos = $basecampClient->getTodos($user);
var_dump($todos);