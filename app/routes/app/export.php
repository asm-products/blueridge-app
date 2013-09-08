<?php
/**
 *  Export Routes
 */

use \BlueRidge\Documents\User;
use \BlueRidge\Providers\Basecamp\BasecampClient;

$app->get('/app/export/csv/', $authenticate($app), function () use ($app) {

    $filename = 'To-Dos-'.date("Ymd").'.csv';

    
    /**
    * @todo Check for valid authenticated session 
    */
    // fetch user data
    $id = $_SESSION['user'];
    $user = $app->dm->find('\BlueRidge\Documents\User', $id);


    $basecampClient = BasecampClient::factory($app)->setAuth($user);
    $todos = $basecampClient->getTodos($user);


    $arrayToCsv = function (array &$todos, $delimiter = ';', $enclosure = '"', $encloseAll = false){
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        echo '"Due Date","Days Overdue","Description","To-do List","Project","Owner","URL"'."\n";

        $output = array();

        /**
         * @todo use iterator
         */
        foreach ( $todos as $todo ) {
            $owner = (!empty($todo['owner']['name']))?$todo['owner']['name']:'';
            $line = '';
            $line .= '"' . $todo['due_on'] . '",';
            $line .= '"' . $todo['overdue_by'] . '",';
            $line .= '"' . $todo['content'] . '",';
            $line .= '"' . $todo['list'] . '",';
            $line .= '"' . $todo['project'] . '",';
            $line .= '"' . $owner . '",';
            $line .= '"' . $todo['url'] . '"';
            echo $line . "\n";            
        }

    };

    $arrayToCsv($todos);

    
    $app->response->headers->set('Content-Type', 'text/csv');
    $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
    
});