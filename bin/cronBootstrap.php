<?php

date_default_timezone_set("Europe/Berlin");

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod'));

/**
 * set include path
 */
set_include_path(implode(PATH_SEPARATOR, array("/app/www/library", get_include_path()))); // TODO make relative

/** 
 * Initialize Zend autoloader
 */
require_once 'Zend/Loader/Autoloader.php';


$autoLoader = Zend_Loader_Autoloader::getInstance();
$autoLoader->registerNamespace("YBCore_");

$config = new Zend_Config_Ini('/app/www/application/configs/application.ini', APPLICATION_ENV); // TODO make relative

$db = Zend_Db::factory($config->database);
Zend_Db_Table_Abstract::setDefaultAdapter($db);