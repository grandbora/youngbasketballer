<?php

require_once 'AbstractRenderHelper.php';

/**
 * Helper to render views
 */
class Zend_View_Helper_RenderViewHelper extends Zend_View_Helper_AbstractRenderHelper
{
    /**
     * user photo image types
     */
    const IMAGETYPE_NONE = null;
    const IMAGETYPE_SQUARE = 'square';
    const IMAGETYPE_SMALL = 'small';
    const IMAGETYPE_NORMAL = 'normal';
    const IMAGETYPE_LARGE = 'large';
    
    /**
     * user name types
     */
    const NAMETYPE_NONE = null;
    const NAMETYPE_FIRST = 'first_name';
    const NAMETYPE_LAST = 'last_name';
    const NAMETYPE_FULL = 'name';

    /**
     * Sets the classNames (if any given)
     * Returns reference to self
     * 
     * @param string cssClassList
     */
    public function renderViewHelper()
    {
        return $this->_setCssClassList(func_get_args());
    }

    /**
     * Renders the user photo and name with a link to profile
     * 
     * @param YBCore_Model_User_Abstract $user
     * @param [optional] string self::IMAGETYPE_* $imageType = self::IMAGETYPE_NORMAL
     * @param [optional] string self::NAMETYPE_* $nameType = self::NAMETYPE_NONE  
     * @param [optional] boolean $enableLink = true
     * @param [optional] boolean $sendToFbProfile = false
     */
    public function userIndicator(YBCore_Model_User_Abstract $user, $imageType = self::IMAGETYPE_NORMAL, $nameType = self::NAMETYPE_NONE, $enableLink = true, $sendToFbProfile = false)
    {
        $this->view->assign('userIndicatorData', array('user' => $user, 'imageType' => $imageType, 'nameType' => $nameType, 'enableLink' => $enableLink, 'sendToFbProfile' => $sendToFbProfile));
        return $this->view->render('template/indicator/userindicator.phtml');
    }
    
    /**
    * Renders the dummy user
    *
    * @param [optional] string self::IMAGETYPE_* $imageType = self::IMAGETYPE_NORMAL
    * @param [optional] string self::NAMETYPE_* $nameType = self::NAMETYPE_NONE
    */
    public function userIndicatorDummy($imageType = self::IMAGETYPE_NORMAL, $nameType = self::NAMETYPE_NONE)
    {
    	$this->view->assign('userIndicatorDummyData', array('imageType' => $imageType, 'nameType' => $nameType));
    	return $this->view->render('template/indicator/userindicatordummy.phtml');
    }

    /**
     * Renders the team name with a link to profile
     * 
     * @param YBCore_Model_Team $team
     * @param [optional] boolean $enableLink = true
     */
    public function teamIndicator(YBCore_Model_Team $team, $enableLink = true)
    {
        $this->view->assign('teamIndicatorData', array('team' => $team, 'enableLink' => $enableLink));
        return $this->view->render('template/indicator/teamindicator.phtml');
    }

    /**
     * Renders information icon with json data
     * 
     * @param [optional] string $content = null
     */
    public function informationIndicator($content)
    {
        $id = $this->_registerView(__FUNCTION__);
        $this->view->assign('informationIndicatorData', array('id' => $id, 'content' => $content));
        return $this->view->render('template/indicator/informationindicator.phtml');
    }

    /**
     * Renders the section header
     * 
     * @param string $title
     * @param string $cssClass 
     * @param [optional] string $informationText = null  
     */
    public function sectionHeader($title, $informationText = null)
    {
        $this->view->assign('sectionHeaderData', array('title' => $title, 'informationText' => $informationText));
        return $this->view->render('template/header/section.phtml');
    }

    /**
     * Renders roster of the team
     * 
     * @param YBCore_Model_Team $team
     * @param [optional] string self::IMAGETYPE_* $imageType = self::IMAGETYPE_SQUARE
     */
    public function teamRoster(YBCore_Model_Team $team, $imageType = self::IMAGETYPE_SQUARE)
    {
        $this->view->assign('teamRosterData', array('roster' => $team->getSortedRoster(), 'imageType' => $imageType));
        return $this->view->render('template/indicator/teamroster.phtml');
    }

    /**
     * Renders the sidebar of the given observed model
     * 
     * @param YBCore_Model_Abstract $observedModel
     */
    public function sideBar(YBCore_Model_Abstract $observedModel)
    {
        $template = null;
        if ($observedModel instanceof YBCore_Model_User_Abstract)
        {
            $this->view->assign('sideBarData', array('observedUser' => $observedModel));
            $template = $this->view->render('template/sidebar/profile/user.phtml');
            
            $this->view->assign('extensionData', array('observedUser' => $observedModel));
            $template .= $this->view->render('template/sidebar/extension.phtml');
        }
        
        if ($observedModel instanceof YBCore_Model_Team)
        {
            $this->view->assign('sideBarData', array('observedTeam' => $observedModel));
            $template = $this->view->render('template/sidebar/profile/team.phtml');
        }
        
        return $template;
    }

    /**
     * Renders the header of the given observed model
     * 
     * @param YBCore_Model_Abstract $observedModel
     */
    public function header(YBCore_Model_Abstract $observedModel)
    {
        if ($observedModel instanceof YBCore_Model_User_Abstract)
        {
            $observedUserTeam = null;
            if (null !== $observedModel->getTeamId())
                $observedUserTeam = $observedModel->getTeam();
            
            $this->view->assign('headerData', array('observedUser' => $observedModel, 'observedUserTeam' => $observedUserTeam));
            return $this->view->render('template/header/user.phtml');
        }
        
        if ($observedModel instanceof YBCore_Model_Team)
        {
            $this->view->assign('headerData', array('observedTeam' => $observedModel));
            return $this->view->render('template/header/team.phtml');
        }
    }

    /**
     * Renders weekView for given eventList
     * 
     * @param md array of YBCore_Model_Event_Abstract
     * @param YBCore_Model_Abstract $observedModel
     * @param string $title
     * @param bool $showDate=true
     */
    public function weekView(array $eventList, YBCore_Model_Abstract $observedModel, $title, $showDate = true)
    {
        $id = $this->_registerView(__FUNCTION__);
        $this->view->assign('weekViewData', array('id' => $id, 'eventList' => $eventList, 'observedModel' => $observedModel, 'title' => $title, 'showDate' => $showDate));
        return $this->view->render('template/calendar/weekview.phtml');
    }

    /**
     * Renders the training program of the given observed model
     * 
     * @param YBCore_Model_Abstract $observedModel
     */
    public function trainingProgram(YBCore_Model_Abstract $observedModel)
    {
        // eliminate TeamOwner here 
        if (false === $observedModel instanceof YBCore_Interface_Connectable)
            return;
        
        $schedule = $observedModel->getSchedule();
        $eventList = $schedule->getTrainingProgram();
        
        return $this->weekView($eventList, $observedModel, "Training Program", false);
    }

    /**
     * Renders the list of given game results
     * 
     * @param array of YBCore_Model_Event_Game_Result $resultList
     * @param YBCore_Model_Team $observedTeam
     * 
     */
    public function gameArchive(array $resultList, YBCore_Model_Team $observedTeam)
    {
        $this->view->assign('gameArchiveData', array('resultList' => $resultList, 'observedTeam' => $observedTeam));
        return $this->view->render('template/grid/gamearchive.phtml');
    }

    /**
     * Renders the given connections of the given model
     *
     * @param YBCore_Interface_Connectable $model
     * @param int $connectionStatus YBCore_Model_Connection_Mapper_*::STATUS
     */
    public function connectionListView(YBCore_Interface_Connectable $model, $connectionStatus)
    {
        $id = $this->_registerView(__FUNCTION__);
        
        $connectionList = $model->getConnectionListByStatus($connectionStatus);
        $this->view->assign('connectionListViewData', array('id' => $id, 'connectionList' => $connectionList, 'observedModel' => $model, 'connectionStatus' => $connectionStatus));
        return $this->view->render('template/connection/connectionlistview.phtml');
    }

    /**
     * Renders the given requests of the given team
     *
     * @param YBCore_Model_Team $observedTeam
     * @param array of YBCore_Model_Connection_Request $requestList
     * @param string $title
     */
    public function requestListView(YBCore_Model_Team $observedTeam, array $requestList, $title)
    {
        $id = $this->_registerView(__FUNCTION__);
        
        $this->view->assign('requestListViewData', array('id' => $id, 'observedTeam' => $observedTeam, 'requestList' => $requestList, 'title' => $title));
        return $this->view->render('template/connection/requestlistview.phtml');
    }

    /**
     * Renders the user grid
     * @todo sql queries(player, coach, TO) can be combined at user mapper to reduce requests
     * 
     * @param array of YBCore_Model_User_Abstract $userList
     */
    public function userGrid(array $userList, $title)
    {
        $id = $this->_registerView(__FUNCTION__);
        
        $this->view->assign('userGridData', array('id' => $id, 'userList' => $userList, 'title' => $title));
        return $this->view->render('template/grid/user.phtml');
    }

    /**
     * Renders the team grid
     * 
     * @param array of YBCore_Model_Team $teamList
     */
    public function teamGrid(array $teamList)
    {
        $this->view->assign('teamGridData', array('teamList' => $teamList));
        return $this->view->render('template/grid/team.phtml');
    }

    /**
     * Renders the game grid
     * 
     * @param array of YBCore_Model_Event_Game_ScheduledGame $scheduledGameList
     */
    public function gameGrid(array $scheduledGameList)
    {
        $this->view->assign('gameGridData', array('gameList' => $scheduledGameList));
        return $this->view->render('template/grid/game.phtml');
    }

    /**
     * Renders the challenge grid
     * 
     * @param array of YBCore_Model_Event_Game_Challenge $challengeList
     */
    public function challengeGrid(array $challengeList)
    {
        $id = $this->_registerView(__FUNCTION__);
        $this->view->assign('challengeGridData', array('challengeList' => $challengeList));
        return $this->view->render('template/grid/challenge.phtml');
    }
}