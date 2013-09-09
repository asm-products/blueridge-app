<?php
/**
 * Provider
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge\Middleware;

use \Slim\Middleware;
use \Guzzle\Http\Client;
use \Guzzle\Cache\DoctrineCacheAdapter;
use \Guzzle\Plugin\Cache\CachePlugin;
use \Doctrine\Common\Cache\ApcCache;
use \Doctrine\Common\Cache\ArrayCache;

class Provider extends Middleware
{
    public function call()
    {
        if(!empty($this->app->config('providers')['basecamp']))
        {   
            $this->app->container->singleton('provider', function () {
                $settings = $this->app->config('providers')['basecamp'];    
                $client = new Client();
                $client->setUserAgent($settings['user_agent']);
                $cachePlugin = new CachePlugin(array(
                    'adapter' => new DoctrineCacheAdapter(new ArrayCache())
                    ));
                $client->addSubscriber($cachePlugin);
                return $client;                
            });
        }

        $this->next->call();
    }
}