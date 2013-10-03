<?php
/**
 * Mail
 * Instantiate mail
 * @author Moses Ngone <mo@mospired.com>
 */

namespace Blueridge\Middleware;

use \Slim\Middleware;

class Mail extends Middleware
{
    public function call()
    {
        if(!empty($this->app->config('services')['mail']))
        {            
            $mail = $this->app->config('services')['mail'];
            $this->app->mail =  new \Mandrill($mail['api_key']);
        }
        $this->next->call();
    }
}