<?php
/**
 * Mail man
 */

function postman_send($app,$user,$template,Array $params){
    $templates= $app->config('templates.path');

    try {
        $message = array(
            'html' => "<h3>Welcome to BlueRidge</h3><p>Your password is {$params['password']}</p>",
            'text' => "Welcome to BlueRidge \n Your password is {$params['password']}",
            'subject' => 'Welcome to BlueRidge',
            'from_email' => 'team@blueridgeapp.com',
            'from_name' => 'BlueRidge',
            'to' => array(
                array(
                    'email' => $user->email,
                    'name' => $user->name
                    )
                ),
            'headers' => array('Reply-To' => 'team@blueridgeapp.com'),
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'tracking_domain' => null,
            'signing_domain' => null,
            'tags' => array('welcome')
            );
        $async = false;
        $ip_pool = 'Main Pool';
        return $app->mailbox->messages->send($message, $async, $ip_pool);


    } catch(Mandrill_Error $e) {
        echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
        throw $e;
    }

}
