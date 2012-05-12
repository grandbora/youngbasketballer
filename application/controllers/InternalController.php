<?php
/**
 * abstract parent controller class for all other controller that are running with a logged in user
 * In other words if user is inside the application (logged in) the controller, that are accessed must be inherited from this
 */
abstract class InternalController extends Zend_Controller_Action
{

    /**
     * user object
     */
    protected $_user;

    /**
     * FB APP instance
     */
    protected $_facebook;

    /**
     * Creates fb object and logged in user and sets them to view
     */
    public function init()
    {
        $this->_facebook = Zend_Registry::get('facebook');

        $ybUserList = array();
        $fbId = $this->_facebook->getUser();

        if (true === empty($fbId))
        $this->_helper->_redirector->gotoSimple('login', 'index');

        $userMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_Abstract');
        try {
            $ybUserList = $userMapper->initializeUser($fbId, YBCore_Model_User_Mapper_User::USERIDTYPE_FBID);
        } catch (Exception $e) {
        }

        if (true === empty($ybUserList))
        $this->_helper->_redirector->gotoSimple('signup', 'index');

        $this->_user = $ybUserList[0];

        $this->view->facebook = $this->_facebook;
        $this->view->loginUser = $this->_user;

        $config = Zend_Registry::get('config');
        $this->view->facebookUrl = $config->facebook->url;

        // @todo move to fb action helper
        if($requestId = $this->getRequest()->getParam('request_ids')){
            try {
                $this->_facebook->deleteAppRequest($requestId, $this->_user->getFbId());
            } catch (Exception $e) {
            }
        }
    }
}