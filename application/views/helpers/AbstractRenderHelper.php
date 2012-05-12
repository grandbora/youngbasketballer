<?php

require_once 'AbstractHelper.php';

/**
 * Helper to render views
 */
abstract class Zend_View_Helper_AbstractRenderHelper extends Zend_View_Helper_AbstractHelper
{
    /**
     * javascript path prefix
     */
    const JS_PREFIX = "/public/js/template/";

    /**
     * container used to keep track of registered views 
     * @var array() 
     */
    private $_viewRegister = array();

    /**
     * Registers the given view
     * Returns the unique id for that view
     * Includes given js files 
     * 
     * @param string $viewName
     * @return int id of the view
     */
    protected function _registerView($viewName)
    {
        if (false === isset($this->_viewRegister[$viewName]))
        {
            $this->view->headScript()->appendFile(self::JS_PREFIX . strtolower($viewName) . '.js');
            $this->_viewRegister[$viewName] = 0;
        }
        
        return $this->_viewRegister[$viewName]++;
    }

    /**
     * Sets the classNames (if any given)
     * Returns reference to self
     * 
     * @param[optional] array $templateCssClassList = array() array of css classes
     */
    protected function _setCssClassList($templateCssClassList = array())
    {
        if (false === empty($templateCssClassList))
            $this->view->assign('templateCssClass', implode(" ", $templateCssClassList));
        else
            $this->view->assign('templateCssClass', null);
        
        return $this;
    }
}