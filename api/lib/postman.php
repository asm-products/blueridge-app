<?php
/**
 * Mail man
 */

function postman_send($app,$user,Array $params)
{

    $template_name = 'blueridge-transactional';
    $template_content = array(array(
        'name' => 'std_content',
        'content' => "
        <h3 class=\"h3\">You are in.</h3>
        <p><strong>Hi *|user_name|*</strong></p>
        <p>Yes! it's that dramatic for us, we are glad to have you on board.</p>
        <p>In connecting with basecamp, you signed up with the same email you use for basecamp and we cooked up a descent password to get you started.</p>
        <p>Here are your credentials</p>
        <p>Email: <code>*|user_email|*</code> <br>Password: <code>*|user_password|*</code></p>
        <p> Thanks a bunch.<br>team@blueridgeapp.com<br><a href=\"https\:\/\/blueridgeapp.com\">https://blueridgeapp.com</a></p>"
        ));

    $message = array(
        'from_email' => 'team@blueridgeapp.com',
        'from_name' => 'BlueRidge Team',
        'to' => array(array('email' => $user->email,'name' => $user->name)),
        'headers' => array('Reply-To' => 'team@blueridgeapp.com'),
        'important' => false,
        'inline_css' => true,
        'bcc_address' => 'team@blueridgeapp.com',
        'merge' => true,
        'merge_vars' => array(
            array(
                'rcpt' => $user->email,
                'vars' => array(
                    array('name' => 'user_name','content' => $user->name),
                    array('name' => 'user_email','content' => $user->email),
                    array('name' => 'user_password','content' => $params['password'])
                    )
                )
            ),
        'tags' => array('welcome,credentials'),
        'google_analytics_domains' => array('blueridgeapp.com'),
        'metadata' => array('website' => 'blueridgeapp.com'),
        );
    $async = false;
    $ip_pool = 'Main Pool';


    try{
        return $app->mailbox->messages->sendTemplate($template_name, $template_content, $message, $async, $ip_pool);
    } catch(Mandrill_Error $e) {    
        echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
        throw $e;
    }

}
