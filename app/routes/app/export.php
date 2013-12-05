<?php
/**
 *  Export Routes
 */

use \Blueridge\Documents\User;
use \Blueridge\Providers\Basecamp;

$app->get('/app/export/csv/', $authenticate($app), function () use ($app,$blueridge) {

    $filename = 'To-Dos-'.date("Ymd").'.csv';

    
    /**
    * @todo Check for valid authenticated session 
    */
    // fetch user data
    $id = base64_decode($_SESSION['user']);
    $user = $blueridge['documentManager']->find('\Blueridge\Documents\User', $id);

    $basecampClient = new Basecamp($app);
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
            $line = '';
            $line .= '"' . $todo['due_on'] . '",';
            $line .= '"' . $todo['overdue_by'] . '",';
            $line .= '"' . $todo['content'] . '",';
            $line .= '"' . $todo['parent']['list_name'] . '",';
            $line .= '"' . $todo['parent']['project_name'] . '",';
            $line .= '"' . $todo['assignee']['name'] . '",';
            $line .= '"' . $todo['href'] . '"';
            echo $line . "\n";            
        }

    };

    $arrayToCsv($todos);

    
    $app->response->headers->set('Content-Type', 'text/csv');
    $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
    
});