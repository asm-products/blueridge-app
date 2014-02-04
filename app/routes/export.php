<?php
/**
 *  Export Routes
 */

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;

/**
 * Return a csv file as an attachment with all the todos from selected projects
 */
$app->get('/app/export/csv/',function () use ($app,$blueridge) {

    $filename = 'To-Dos-'.date("Ymd").'.csv';
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

    $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

    $collection = $todoQr->fetchByUser($user);
    $todos = Array();
    foreach ($collection as $todo ) {
        $todos[]=$todo->toExport();
    }
    $columnTitles = ['Due Date','Days Overdue','Description','To-do List','Project','Owner','URL'];
    array_unshift($todos,$columnTitles);

    $out = fopen('php://output', 'w');
    foreach ($todos as $todo ) {
        fputcsv($out, $todo,',');
    }
    fclose($out);

    $app->response->headers->set('Content-Type', 'text/csv');
    $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

});