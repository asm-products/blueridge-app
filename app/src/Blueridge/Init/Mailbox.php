<?php
/**
 * Mail
 * Instantiate mail
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge\Init;

use \Slim\Middleware;

class Mailbox extends Middleware
{
    public function call()
    {
        if(!empty($this->app->config('services')['mailbox']))
        {
            $this->app->container->singleton('mail', function () {                
                $mailbox = $this->app->config('services')['mail'];
                return new \Mandrill($mailbox['api_key']);
            });
        }

        $this->next->call();

    }


}