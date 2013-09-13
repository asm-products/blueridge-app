<?php
/**
 * Oauth
 * Create Oauth client configs
 * @author Moses Ngone <mo@mospired.com>
 */

namespace Blueridge\Middleware;

use \Slim\Middleware;

class Oauth extends Middleware
{    
    public function call()
    {
        $configs = $this->app->config('providers');
        if(!empty($configs['basecamp'])){
            $this->app->basecampConfig = $this->initBasecampClient($configs['basecamp']);
        }
        $this->next->call();
    }

    protected function initBasecampClient($settings)
    {
        $clientConfig = new ClientConfig(
            [
            'authorize_endpoint' => $settings['auth_url'],
            'client_id' => $settings['client_id'],
            'client_secret' => $settings['client_secret'],
            'token_endpoint' => $settings['token_url'],
            'redirect_uri' => $settings['redirect_uri'],
            'enable_debug' => true
            ]
            );
        return $clientConfig;
    }
}