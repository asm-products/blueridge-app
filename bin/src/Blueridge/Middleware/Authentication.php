<?php
/**
 * Blueridge 
 * 
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Middleware;

use Slim\Middleware;
use Blueridge\Application;
use Blueridge\Utilities\Doorman;

/**
 * Authentication middleware
 *
 * Checks if user is authenticated when user visiting secured URI. Will redirect
 * a user to login if they attempt to visit a secured URI and are not authenticated
 */
class Authentication extends Middleware
{
    /**
     * Blueridge application container
     * @var Blueridge\Application
     */
    private $blueridge;
    
    /**
     * Public Constructor
     * @param Bluridge\Application $blueridge
     */
    public function __construct(Application $blueridge)
    {
    	$this->blueridge = $blueridge;
    }

    /**
     * Uses 'slim.before.router' to check for authentication when visitor attempts
     * to access a secured URI. Will redirect unauthenticated user to login page.
     */
    public function call()
    {
    	$app = $this->app;
    	$blueridge = $this->blueridge;

    	$authenticate = function () use ($app, $blueridge) {            
    		$securedUrls = !empty($blueridge['configs']['secured.urls']) ? $blueridge['configs']['secured.urls'] : [];
    		foreach ($securedUrls as $url) {                
    			$urlPattern = '@^' . $url . '$@';
    			if (preg_match($urlPattern, $app->request()->getPathInfo()) === 1 && $blueridge['authenticationService']->hasIdentity() === false) {
    				return $app->redirect('/');
    			}             
    		}            
        };

        $this->app->hook('slim.before.router', $authenticate);

        $this->next->call();
    }


}