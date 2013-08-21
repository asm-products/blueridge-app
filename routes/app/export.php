<?php
/**
 *  Export Routes
 */

$app->get('/app/export/csv', function ($format) {
    
    $protocol = (!empty($_SERVER['HTTPS']))?'https://':'http://';
    $base= $protocol.$_SERVER['SERVER_NAME'];
    $filename = 'To-Dos-'.date("Ymd").'.csv';
    
    /**
    * @todo Check for valid authenticated session 
    */
    // fetch user data




    $app->response->headers->set('Content-Type', 'text/csv');
    $app->response->headers->set('Content-Disposition: attachment', 'filename="'.$filename.'"');
    
});

/*

function arrayToCsv( array &$todos, $delimiter = ';', $enclosure = '"', $encloseAll = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');
    echo '"Due Date","Days Overdue","Description","To-do List","Project","Owner","URL"'."\n";

    $output = array();
    foreach ( $todos as $todo ) {
        foreach($todo as $value)
        {
            $line = '';
            $line .= '"' . $value['dueDate'] . '",';
            $line .= '"' . $value['overDueBy'] . '",';
            $line .= '"' . $value['content'] . '",';
            $line .= '"' . $value['list'] . '",';
            $line .= '"' . $value['projectName'] . '",';
            $line .= '"' . $value['owner']['name'] . '",';
            $line .= '"' . $value['url'] . '"';
            echo $line . "\n";

        }
    }

}*/