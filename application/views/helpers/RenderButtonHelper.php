<?php

require_once 'AbstractRenderHelper.php';

/**
 * Helper to render button views
 */
class Zend_View_Helper_RenderButtonHelper extends Zend_View_Helper_AbstractRenderHelper
{
    const BUTTON_VIEW = "buttonView";

    /**
     * Container of the reference to Zend_View_Helper_TextHelper object 
     */
    private $_textHelper;

    /**
     * Registers the given button
     * Includes button.js file
     * Returns buttonDataList, containing unique id of the button and the output of the button.phtml
     *
     * @param string $buttonText
     * @return array
     */
    protected function _registerView($cssClass, $buttonText)
    {
        $id = parent::_registerView(self::BUTTON_VIEW);
        $buttonHtmlId = self::BUTTON_VIEW . '_' . $id;
        
        $this->view->assign(self::BUTTON_VIEW . 'Data', array('htmlId' => $buttonHtmlId, 'text' => $buttonText, 'cssClass' => $cssClass));
        $output = $this->view->render('template/button/buttonview.phtml');
        
        return array('buttonHtmlId' => $buttonHtmlId, 'output' => $output);
    }

    /**
     * Sets the classNames (if any given)
     * Returns reference to self
     * 
     * @todo remove if _setCssClassList is not used
     * 
     * @param string cssClassList
     */
    public function renderButtonHelper()
    {
        $this->_textHelper = $this->view->textHelper();
        return $this->_setCssClassList(func_get_args());
    }

    /**
     * Renders remove connection button for the given connection
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $observedModel
     * @param YBCore_Model_Connection_Abstract $connection
     * @param [optional] bool $isExecutingRequest = false
     */
    public function removeConnectionButton(YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $observedModel, YBCore_Model_Connection_Abstract $connection, $isExecutingRequest = false)
    {
        if (true === $observedModel instanceof YBCore_Model_User_Abstract && $observedModel->getId() !== $loginUser->getId())
            return;
        
     // only this team's owner and HC is authorized to remove connections (HC can remove requests only)
        if ($observedModel instanceof YBCore_Model_Team && ($loginUser instanceof YBCore_Model_User_Player || $loginUser->getTeamId() !== $observedModel->getId()))
            return;
        
        $buttonTextData = $this->_textHelper->getRemoveConnectionButtonText($loginUser, $observedModel, $connection, $isExecutingRequest);
        $buttonData = $this->_registerView(__FUNCTION__, $buttonTextData['short']);
        $this->view->assign('removeConnectionDialogData', 
        array('buttonHtmlId' => $buttonData['buttonHtmlId'], 'title' => $buttonTextData['long'], 'header' => $buttonTextData['header'], 'connection' => $connection));
        $dialogView = $this->view->render('template/button/data/removeconnection.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the modify connection button for the given connection
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $observedModel
     * @param YBCore_Model_Connection_Abstract $connection
     */
    public function modifyConnectionButton(YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $observedModel, YBCore_Model_Connection_Abstract $connection)
    {
        $buttonText = null;
        // contracts can not be modified, only removed
        if (YBCore_Model_Connection_Mapper_Contract::STATUS === $connection->getStatus())
            return;
        
     // modify buttons for team (add training for ex.) not implemented yet
        if ($observedModel instanceof YBCore_Model_Team)
            return;
        
     // eliminate TO here
        if ($observedModel->getId() !== $loginUser->getId())
            return;
        
     // player cannot modify his/her own offer (add training etc.)
        if (true === $observedModel instanceof YBCore_Model_User_Player && YBCore_Model_Connection_Mapper_Contract::TYPE_INDIVIDUAL === $connection->getType())
            return;
        
        $buttonTextData = $this->_textHelper->getModifyConnectionButtonText($loginUser, $observedModel, $connection);
        $buttonData = $this->_registerView(__FUNCTION__, $buttonTextData['short']);
        $this->view->assign('modifyConnectionDialogData', 
        array('buttonHtmlId' => $buttonData['buttonHtmlId'], 'title' => $buttonTextData['long'], 'header' => $buttonTextData['header'], 'connection' => $connection));
        $dialogView = $this->view->render('template/button/data/modifyconnection.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the create connection button 
     *
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Model_User_Abstract $targetUser
     * @param [optional] bool $isExecutingRequest = false
     */
    public function createConnectionButton(YBCore_Model_User_Abstract $loginUser, YBCore_Model_User_Abstract $targetUser, $isExecutingRequest = false)
    {
        
        if ($loginUser instanceof YBCore_Model_User_Player && $targetUser instanceof YBCore_Model_User_Coach)
        {
            if (in_array($targetUser->getId(), $loginUser->getIndividualContractorIdList()))
                return;
            
            if (in_array($targetUser->getId(), $loginUser->getIndividualOfferIdList()))
                return;
        
        } elseif ($loginUser instanceof YBCore_Model_User_Coach && $targetUser instanceof YBCore_Model_User_Player)
        {
            if (null === $loginUser->getTeam())
                return;
            
            if (in_array($targetUser->getId(), $loginUser->getTeam()->getRequestIdList()))
                return;
        
        } elseif ($loginUser instanceof YBCore_Model_User_TeamOwner)
        {
            if (in_array($targetUser->getId(), $loginUser->getTeam()->getEmployeeIdList()))
                return;
            
            if (in_array($targetUser->getId(), $loginUser->getTeam()->getOfferIdList()))
                return;
            
            $coachList = $loginUser->getTeam()->getCoachList();
            if ($targetUser instanceof YBCore_Model_User_Coach)
            {
                if (false === empty($coachList))
                    return;
            }
        } else
        {
            return;
        }
        
        $buttonTextData = $this->_textHelper->getCreateConnectionButtonText($loginUser, $targetUser, $isExecutingRequest);
        $buttonData = $this->_registerView(__FUNCTION__, $buttonTextData['short']);
        $this->view->assign('createConnectionDialogData', 
        array('buttonHtmlId' => $buttonData['buttonHtmlId'], 'text' => $buttonTextData['short'], 'title' => $buttonTextData['long'], 'header' => $buttonTextData['header'], 'targetUser' => $targetUser, 
        'disableDayList' => $buttonTextData['disableDayList'], 'isRequest' => $buttonTextData['isRequest'], 'contractDayList' => $buttonTextData['contractDayList']));
        $dialogView = $this->view->render('template/button/data/createconnection.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the execute request button
     * Decides which button to render then calls the appropriate method  
     *
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Model_Connection_Request $request
     */
    public function executeRequestButton(YBCore_Model_User_Abstract $loginUser, YBCore_Model_Connection_Request $request)
    {
        if (false === $loginUser instanceof YBCore_Model_User_TeamOwner || $request->getEmployerId() !== $loginUser->getTeamId())
            return;
        
        $this->_setCssClassList(array(__FUNCTION__));
        
        $team = $loginUser->getTeam();
        $employee = $request->getEmployee();
        if (in_array($request->getEmployeeId(), $team->getEmployeeIdList()))
            // fire, cancel contract button
            return $this->removeConnectionButton($loginUser, $team, $team->getContractOfEmployee($employee), true);
        else
            // transfer, make offer button
            return $this->createConnectionButton($loginUser, $employee, true);
    }

    /**
     * Renders the accept challenge button
     *
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Model_Event_Game_Challenge $challenge
     */
    public function acceptChallengeButton(YBCore_Model_User_Abstract $loginUser, YBCore_Model_Event_Game_Challenge $challenge)
    {
        if (false === $loginUser instanceof YBCore_Model_User_TeamOwner || $challenge->getHomeTeamId() === $loginUser->getTeamId())
            return;
        
        $teamSchedule = $loginUser->getTeam()->getSchedule();
        $teamSchedule->loadGameEventList(YBCore_Model_Event_Game_Mapper_ScheduledGame::STATUS);
        if (false === $teamSchedule->getEventCollection()->isEmpty())
            return;
        
        $buttonData = $this->_registerView(__FUNCTION__, 'Accept');
        $this->view->assign('acceptChallengeDialogData', array('buttonHtmlId' => $buttonData['buttonHtmlId'], 'game' => $challenge, 'loginUser' => $loginUser));
        $dialogView = $this->view->render('template/button/data/acceptchallenge.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the withdraw challenge button
     *
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Model_Event_Game_Challenge $challenge
     */
    public function withdrawChallengeButton(YBCore_Model_User_Abstract $loginUser, YBCore_Model_Event_Game_Challenge $challenge)
    {
        if (false === $loginUser instanceof YBCore_Model_User_TeamOwner || $challenge->getHomeTeamId() !== $loginUser->getTeamId())
            return;
        
        $buttonData = $this->_registerView(__FUNCTION__, 'Withdraw');
        $this->view->assign('withdrawChallengeDialogData', array('buttonHtmlId' => $buttonData['buttonHtmlId'], 'game' => $challenge));
        $dialogView = $this->view->render('template/button/data/withdrawchallenge.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the create challenge button
     * Declared private because necessary security/logic checks are done in 
     * calling function(toggleChallenge). Therefore should not be called directly from view
     * 
     * @param YBCore_Model_User_TeamOwner $loginUser
     */
    private function _createChallengeButton(YBCore_Model_User_TeamOwner $loginUser)
    {
        $buttonData = $this->_registerView("createChallengeButton", 'Create Challenge');
        $this->view->assign('createChallengeDialogData', array('buttonHtmlId' => $buttonData['buttonHtmlId']));
        $dialogView = $this->view->render('template/button/data/createchallenge.phtml');
        return $buttonData['output'] . $dialogView;
    }

    /**
     * Renders the toggle challenge button
     * Decides which button to render (create/remove or none) then calls the appropriate method  
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     */
    public function toggleChallengeButton(YBCore_Model_User_Abstract $loginUser)
    {
        if (false === $loginUser instanceof YBCore_Model_User_TeamOwner)
            return;
        
        $teamSchedule = $loginUser->getTeam()->getSchedule();
        $teamSchedule->loadGameEventList(YBCore_Model_Event_Game_Mapper_Challenge::STATUS);
        $challengeList = $teamSchedule->getEventCollection();
        
        // removed from UI
        //   return $this->withdrawChallengeButton($loginUser, $challengeList[0]);
        if (false === $challengeList->isEmpty())
            return;
        
        $teamSchedule->loadGameEventList(YBCore_Model_Event_Game_Mapper_ScheduledGame::STATUS);
        if (false === $teamSchedule->getEventCollection()->isEmpty())
            return;
        
        return $this->_createChallengeButton($loginUser);
    }
}