<?php
/**
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * config object
     */
    private $_config;

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        $this->_config = $config;
    }

    protected function _initDatabase()
    {
        $db = Zend_Db::factory($this->_config->database);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    protected function _initFacebook()
    {
        $facebook = new FacebookApi_Extension(array('appId' => $this->_config->facebook->appId, 'secret' => $this->_config->facebook->secret));
        Zend_Registry::set('facebook', $facebook);
    }

    protected function _initYBCoreUtility()
    {
        YBCore_Utility_DateTime::$gameTime = $this->_config->utility->gameTime;
        YBCore_Utility_DateTime::$trainingTime = $this->_config->utility->trainingTime;
    }
}

