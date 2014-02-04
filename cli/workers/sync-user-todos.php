#!/usr/bin/php
<?php
/**
 * Blueridge
 * Sync Todos
 */

require realpath(dirname(__FILE__).'/../init.php');

use Blueridge\Documents\User;
use Blueridge\Application;


$blueridge = new Application();
$userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');

$users = $userQr->fetchAll();
foreach ($users as $user) {
    Resque::enqueue('sync', 'Blueridge\Jobs\FetchBasecampTodos', ['userid'=>$user->id]);
}