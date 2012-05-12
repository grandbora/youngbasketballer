<?php
/**
 * Abstract Helper
 */
abstract class Zend_View_Helper_AbstractHelper
{

    /**
     * @var Zend_View_Interface 
     */
    public $view;

    /**
     * Sets the view field 
     * @param Zend_View_Interface $view
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}