<?php
class IndexController extends Zend_Controller_Action
{

    /**
     * user object
     */
    private $_user;

    /**
     * FB APP instance
     */
    private $_facebook;

    public function init()
    {
        $this->_helper->layout()->setLayout('index');
        $this->_facebook = Zend_Registry::get('facebook');
        
        $config = Zend_Registry::get('config');
        $this->view->facebookUrl = $config->facebook->url;
    }

    public function loginAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $fbId = $this->_facebook->getUser();
        if ($fbId)
          $this->_helper->_redirector->gotoSimple('userschedule', 'profile');
        
        // @todo save one redirect by adding a redirect_uri param :  'redirect_uri' => $redirectUrl,
        $loginParamList = array( 'scope' => Zend_Registry::get('config')->facebook->scope->toArray());
        $this->view->loginUrl = $this->_facebook->getLoginUrl($loginParamList);
    }

    public function signupAction()
    {}

    // @todo handle error/exception cases, currently redirecting
    public function createuserAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $fbId = $this->_facebook->getUser();
        if (true === empty($fbId))
            $this->_helper->_redirector->gotoSimple('login', 'index');
        
        $userMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_Abstract');
        $ybUserList = $userMapper->initializeUser($fbId, YBCore_Model_User_Mapper_User::USERIDTYPE_FBID);
        
        if (false === empty($ybUserList))
            $this->_helper->_redirector->gotoSimple('userschedule', 'profile');
        
        $type = (int) $this->getRequest()->getParam('type');
        $position = $this->getRequest()->getParam('position');
        $teamName = null;
        
        $userClassName = null;
        switch ($type)
        {
            case YBCore_Model_User_Mapper_Player::TYPE:
                $userClassName = 'YBCore_Model_User_Player';
                break;
            case YBCore_Model_User_Mapper_Coach::TYPE:
                $userClassName = 'YBCore_Model_User_Coach';
                break;
            case YBCore_Model_User_Mapper_TeamOwner::TYPE:
                $userClassName = 'YBCore_Model_User_TeamOwner';
                $teamName = str_replace(" ", "", $this->getRequest()->getParam('teamname'));
                if (5 > strlen($teamName))
                    $this->_helper->_redirector->gotoSimple('login', 'index');
                break;
        }
        
        $newUser = new $userClassName();
        $newUser->setFbId($fbId);
        $newUser->setPosition($position);
        $newUser->setLineUp(0);
        $balance = Zend_Registry::get('config')->utility->startingBalance;
        $newUser->setBalance($balance);
        
        $profile = $this->_facebook->api($fbId);
        $newUser->setFbName($profile['name']);
        $newUser->setFbFirstName($profile['first_name']);
        $newUser->setFbMiddleName($profile['middle_name']);
        $newUser->setFbLastName($profile['last_name']);
        $newUser->setFbLink($profile['link']);
        
        $newUser->save();
        
        if ($newUser instanceof YBCore_Model_User_TeamOwner)
        {
            $newTeam = new YBCore_Model_Team();
            $newTeam->setName($teamName);
            $newTeam->setOwnerId($newUser->getId());
            $newTeam->save();
        }
    }
}