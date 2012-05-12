<?php
require_once 'InternalController.php';

/**
 * Contains ajax actions for html tex of the views 
 * 
 * @todo remove die(), handle exceptions
 */
class ReadyviewController extends InternalController
{

    // @todo handle this auto by helpers, use json action helper
    // ajaxcontenhelper/contextswitcher maybe?
    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function weekviewdialogAction()
    {
        $eventList = $this->getRequest()->getParam('eventList');
        $this->view->eventList = $eventList;
    }
}