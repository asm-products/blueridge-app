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
        <h3 class=\"h3\">Congratulations!</h3>
        <p><strong>Hi *|user_name|*</strong></p>
        <p>Welcome to BlueRidge. We're really glad to have you on board.</p>
        <p>We created your BlueRidge account using the same email address you use for your Basecamp account. That keeps things simple for everyone. Feel free to change it to whatever you want, though. It's your account. ;)</p>
    		<p>&nbsp;</p>
    		<p><strong>Your BlueRidge account info:</strong><br>Email: <code>*|user_email|*</code> <br>Password: <code>*|user_password|*</code></p>
    		<p><em>Keep your password safe.<br>You'll need it to sign in again.</em></p>
    		<p>&nbsp;</p>
    		<p>We hope BlueRidge makes managing your to-dos easier, but if not, we're here to help. If you ever have any questions just email team@blueridgeapp.com and we'll reply as soon as humanly possible.</p>
    		<p>&nbsp;</p>
        <p>Thanks again for using BlueRidge. We really appreciate it.</p>
        <p>J and Mo<br>team@blueridgeapp.com<br><a href=\"https\:\/\/blueridgeapp.com\">https://blueridgeapp.com</a></p>"
        ));

    $message = array(
        'from_email' => 'team@blueridgeapp.com',
        'from_name' => 'BlueRidge Team',
        'to' => array(array('email' => $user->email,'name' => "{$user->firstName} {$user->lastName}")),
        'headers' => array('Reply-To' => 'team@blueridgeapp.com'),
        'important' => false,
        'inline_css' => true,
        'bcc_address' => 'team@blueridgeapp.com',
        'merge' => true,
        'merge_vars' => array(
            array(
                'rcpt' => $user->email,
                'vars' => array(
                    array('name' => 'user_name','content' => $user->firstName),
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
