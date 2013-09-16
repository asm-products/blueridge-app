<?php
/**
 * Basecamp Client
 */

namespace Blueridge\Providers\Basecamp;

use \Doctrine\Common\Cache\FilesystemCache;
use \Guzzle\Common\Collection;
use \Guzzle\Service\Client;
use \Guzzle\Service\Description\ServiceDescription;
use \Guzzle\Common\Event;
use \Guzzle\Common\Exception\InvalidArgumentException;
use \Guzzle\Cache\DoctrineCacheAdapter;
use \Guzzle\Plugin\Cache\CachePlugin;


class BasecampClient extends Client
{
    /**
     * @param array $config
     * @return \Guzzle\Service\Client|BasecampClient
     * @throws \Guzzle\Common\Exception\InvalidArgumentException
     */
    public static function factory($config = array())
    {

        $default = array(
            'base_url'      => 'https://basecamp.com/2011887/api/{version}/',
            'version'       => 'v1',
            'auth_method'   => 'oauth',
            'access_token'  => null
            );
        $required = array('user_agent');
        $config = Collection::fromConfig($config, $default, $required);
        $client = new self($config->get('base_url'), $config);


        /**
         * Attach Access Token
         */
        if (! isset($config['access_token'])) {
            throw new InvalidArgumentException("Config must contain token when using oath");
        }
        $authoritzation = sprintf('Bearer %s', $config['access_token']);
        
        if (! isset($authoritzation)) {
            throw new InvalidArgumentException("Config must contain valid authentication method");
        }

        /**
         * Attach Cache Plugin
         */
        $cachePlugin = new CachePlugin([
            'adapter' => new DoctrineCacheAdapter(new FilesystemCache(CACHE_DIR.'/basecamp'))
            ]);

        $client->addSubscriber($cachePlugin);

        // Attach a service description to the client
        // $description = ServiceDescription::factory(__DIR__ . '/service.php');
        // $client->setDescription($description);

        // Set required User-Agent
        $client->setUserAgent($config['user_agent']);
        

        $client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($authoritzation) {
            $event['request']->addHeader('Authorization', $authoritzation);

        });

        return $client;
    }
}