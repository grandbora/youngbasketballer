<?php

/**
 * @todo remember pshashe
 *
 */

date_default_timezone_set("Europe/Berlin");

/**
 * Define path to application directory
 * Define application environment
 */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod'));

/**
 * add library directory to include path 
 * must be done before zend application initialized
 */
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path())));

/** 
 * Zend_Application 
 */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();
