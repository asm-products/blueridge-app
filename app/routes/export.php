<?php
/**
 *  Export Routes
 */

use Blueridge\Documents\User;
use Blueridge\Documents\Todo;

$app->get('/app/export/csv/',function () use ($app,$blueridge) {

    $filename = 'To-Dos-'.date("Ymd").'.csv';
    $userQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\User');
    $todoQr= $blueridge['documentManager']->getRepository('\Blueridge\Documents\Todo');

    $user = $userQr->findOneById($blueridge['authenticationService']->getIdentity());

    $collection = $todoQr->fetchByUser($user);
    $todos = Array();
    foreach ($collection as $todo ) {
        $todos[]=$todo->toArray();
    }

    $arrayToCsv = function (array &$todos, $delimiter = ';', $enclosure = '"', $encloseAll = false){
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        echo '"Due Date","Days Overdue","Description","To-do List","Project","Owner","URL"'."\n";

        $output = array();

        /**
         * @todo use iterator
         */
        foreach ( $todos as $todo ) {


            $line = '';
            $line .= '"' . $todo['due_on'] . '",';
            $line .= '"' . $todo['overdue_by'] . '",';
            $line .= '"' . $todo['content'] . '",';
            $line .= '"' . $todo['rel']['list_name'] . '",';
            $line .= '"' . $todo['rel']['project']['name'] . '",';
            $line .= '"' . $todo['assignee']['name'] . '",';
            $line .= '"' . $todo['rel']['href'] . '"';
            echo $line . "\n";
        }

    };

    $arrayToCsv($todos);


    $app->response->headers->set('Content-Type', 'text/csv');
    $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

});