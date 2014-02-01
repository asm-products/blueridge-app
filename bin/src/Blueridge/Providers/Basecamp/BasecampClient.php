<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Providers\Basecamp;

use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Common\Event;
use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Cache\Zf2CacheAdapter;
use Guzzle\Cache\DoctrineCacheAdapter;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\ApcCache;

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
            'base_url'      => 'https://basecamp.com/',
            'version'       => 'v1',
            'token'         => null,
            'user_agent'      => null,
            'auth_method'   => 'oauth',
            );
        $required = [];
        $config = Collection::fromConfig($config, $default, $required);
        $client = new self($config->get('base_url'), $config);


        if (empty($config['token'])) {
            throw new InvalidArgumentException("Config must contain token when using oath");
        }
        $authorization = sprintf('Bearer %s', $config['token']);

        if (! isset($authorization)) {
            throw new InvalidArgumentException("Config must contain valid authentication method");
        }

        // Attach a service description to the client
        $description = ServiceDescription::factory(__DIR__ . '/Resources/service.php');
        $client->setDescription($description);

        // Set required User-Agent
        $client->setUserAgent($config['user_agent']);

        $client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($authorization) {
            $event['request']->addHeader('Authorization', $authorization);

        });

        // Add cache plugin

        $cachePlugin = new CachePlugin(['storage' => new DefaultCacheStorage(new DoctrineCacheAdapter(new ApcCache))]);
        $client->addSubscriber($cachePlugin);


        return $client;
    }
}