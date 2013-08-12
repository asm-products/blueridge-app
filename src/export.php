<?php

$protocol = (!empty($_SERVER['HTTPS']))?'https://':'http://';
$base= $protocol.$_SERVER['SERVER_NAME'];
if (empty($_COOKIE['_blrdgapp_j49']))
{

    header("Location: ".$base);
}


$filename = 'To-Dos-'.date("Ymd").'.csv';
header("Content-Type: text/csv;");
header('Content-Disposition: attachment; filename="'.$filename.'"');

$userkey = base64_decode($_COOKIE['_blrdgapp_j49']);
$url = "{$base}/api/users/{$userkey}/todos";

$file = json_decode(file_get_contents($url),true);

$data = arrayToCsv($file);

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

}




