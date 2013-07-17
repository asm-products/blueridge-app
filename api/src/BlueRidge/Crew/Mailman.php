<?php
/**
 * Mail man
 */

namespace BlueRidge\Crew;

class Mailman 
{
    public static function send($mailbox,$user,$template){
        try {
            $message = array(
                'html' => '<p>Welcome to BlueRidge</p>',
                'text' => 'Welcome to BlueRidge',
                'subject' => 'Welcome to BlueRidge',
                'from_email' => 'dev@blueridgeapp.com',
                'from_name' => 'BlueRidge',
                'to' => array(
                    array(
                        'email' => $user->email,
                        'name' => $user->name
                        )
                    ),
                'headers' => array('Reply-To' => 'dev@blueridgeapp.com'),
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
            return $mailbox->messages->send($message, $async, $ip_pool);
        

        } catch(Mandrill_Error $e) {
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            throw $e;
        }

    }

}