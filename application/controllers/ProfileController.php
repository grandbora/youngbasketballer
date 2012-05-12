<?php
require_once 'InternalController.php';

/**
 * Contains actions to show profile and its elements of the model objects
 */
class ProfileController extends InternalController
{

    public function userscheduleAction()
    {
        $this->_helper->viewRenderer('schedule');
        $observedUser = $this->_getObservedUser();
        $schedule = $observedUser->getSchedule();
        $this->view->observedModel = $observedUser;
        $this->view->eventList = $schedule->getUpcomingEvents();
    }

    public function teamscheduleAction()
    {
        $this->_helper->viewRenderer('schedule');
        $observedTeam = $this->_getObservedTeam();
        $schedule = $observedTeam->getSchedule();
        $this->view->observedModel = $observedTeam;
        $this->view->eventList = $schedule->getUpcomingEvents();
        
        $gameMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_Event_Game_Result');
        $this->view->resultList = $gameMapper->loadGameResultListByTeamId($observedTeam->getId());
    }

    public function userconnectionAction()
    {
        $observedUser = $this->_getObservedUser();
        
        // redirect to self if TO is sent , this case should not happen
        if ($observedUser instanceof YBCore_Model_User_TeamOwner)
            $observedUser = $this->_user;
        
        $this->view->observedUser = $observedUser;
    }

    public function teamconnectionAction()
    {
        $this->view->observedTeam = $this->_getObservedTeam();
    }

    public function teamrosterAction()
    {
        $this->view->observedTeam = $this->_getObservedTeam();
    }

    /**
     * Returns the user whose profile is viewed
     * Default is logged in User
     * 
     * @return YBCore_Model_User_Abstract
     */
    private function _getObservedUser()
    {
        $observedUserId = $this->getRequest()->getParam('id');
        $ybUserList = array();
        $observedUser = null;
        if (false === empty($observedUserId))
        {
            $userMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_Abstract');
            $ybUserList = $userMapper->initializeUser($observedUserId);
        }
        if (false === empty($ybUserList))
            $observedUser = $ybUserList[0];
        else
            $observedUser = $this->_user;
        
        return $observedUser;
    }

    /**
     * Returns the team whose profile is viewed
     * 
     * @return YBCore_Model_Team
     */
    private function _getObservedTeam()
    {
        $observedTeamId = $this->getRequest()->getParam('id');
        if (true === empty($observedTeamId))
            $this->_helper->_redirector->gotoSimple('login', 'index');
        
        $observedTeam = new YBCore_Model_Team($observedTeamId);
        return $observedTeam;
    }
}