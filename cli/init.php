<?php
/**
 * Blueridge
 * Bootstrap Cli
 */

include "/var/www/env.php";

// Set Constants
defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(dirname(__FILE__).'/../'));
defined('CLI_PATH') || define('CLI_PATH', APPLICATION_ROOT.'/cli');
defined('CACHE_DIR') || define('CACHE_DIR', APPLICATION_ROOT.'/cache');
defined('APP_PATH') || define('APP_PATH', APPLICATION_ROOT.'/app');
defined('API_PATH') || define('API_PATH', APPLICATION_ROOT.'/api');
defined('BIN_PATH') || define('BIN_PATH', APPLICATION_ROOT.'/bin');


require APPLICATION_ROOT.'/vendor/autoload.php';