<?php
/**
 * Db
 * Instantiate Db Connection
 * @author Moses Ngone <mo@mospired.com> @mosesngone
 */

namespace BlueRidge\Init;

use \Slim\Middleware;

class Db extends Middleware
{
    public function call()
    {
                
        if(!empty($this->app->config('database')))
        {
            $this->app->container->singleton('db', function () {
                $db_connect = $this->app->config('database');
                $connection_url = "mongodb://{$db_connect['host']}:{$db_connect['port']}/{$db_connect['name']}";
                $client= new \MongoClient($connection_url);
                return $client->selectDB($db_connect['name']);
            });
        }

        $this->next->call();

    }


}