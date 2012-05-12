<?php
/**
 * Helper to do various css and frontend functions
 */
class Zend_View_Helper_RequestHelper
{

    /**
     * variable to store zend request instance
     */
    private $_zendRequest;

    /**
     * Returns reference to self
     */
    public function requestHelper()
    {
        if (null === $this->_zendRequest)
            $this->_zendRequest = Zend_Controller_Front::getInstance()->getRequest();
        return $this;
    }

    /**
     * Returns 'active' if the given action name is the current one, otherwise returns null
     * 
     * @param string $actionName
     */
    public function isActive($actionName)
    {
        return $this->_zendRequest->getActionName() === $actionName ? 'active' : null;
    }

    /**
     * Returns name of the event to listen, according to request type
     * 
     * @param string
     */
    public function getStartEventName()
    {
        return true === $this->_zendRequest->isXmlHttpRequest() ? 'ajaxCompleted.yb' : 'ready';
    }
}