<?php
require_once 'InternalController.php';

/**
 * Contains ajax actions for json data
 *
 * @todo remove die(), handle exceptions
 */
class ReadydataController extends InternalController
{

    // @todo handle this auto by helpers, use json action helper
    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function removeconnectionAction()
    {
        $connectionId = $this->getRequest()->getParam('id');
        try
        {
            $this->_user->removeConnection((int) $connectionId);
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }
    }

    public function modifyconnectionAction()
    {
        $connectionId = $this->getRequest()->getParam('id');
        try
        {
            $this->_user->modifyConnection((int) $connectionId);
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }
        $this->_makeFbAppRequestModifyConnection($connectionId);
    }

    /**
     * makes an fb app request through fb api
     *
     * @todo move this func. to an action helper or etc.
     *
     * @param int $connectionId
     */
    private function _makeFbAppRequestModifyConnection($connectionId)
    {
        $connection = new YBCore_Model_Connection_Offer($connectionId);
        $employer = $connection->getEmployer();
        $targetUserId = null;
        if ($employer instanceof YBCore_Model_Team) {
            $targetUserId = $employer->getOwner()->getFbId();
        }elseif ($employer instanceof YBCore_Model_User_Player) {
            $targetUserId = $employer->getFbId();
        }
        $message =$this->view->textHelper()->getModifyConnectionAppRequestText($this->_user);
        try {
            $this->_facebook->makeAppRequest($targetUserId,$message);
        } catch (FacebookApiException $e) {
        }
    }

    public function createconnectionAction()
    {
        $targetUserId = (int) $this->getRequest()->getParam('id');
        $dayList = $this->getRequest()->getParam('daylist');
        if (null === $dayList)
        $dayList = array();
        $salary = (int) $this->getRequest()->getParam('salary');

        try
        {
            $this->_user->createConnection($targetUserId, $dayList, $salary);
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }

       	$this->_makeFbAppRequestCreateConnection($targetUserId);
    }


    /**
     * makes an fb app request through fb api
     *
     * @todo move this func. to an action helper or etc.
     *
     * @param int $targetUserId
     */
    private function _makeFbAppRequestCreateConnection($targetUserId)
    {
        $message =$this->view->textHelper()->getCreateConnectionAppRequestText($this->_user);
        try {
            $this->_facebook->makeAppRequest($targetUserId, $message);
        } catch (FacebookApiException $e) {
            // non critical fb api ex.
        }
    }


    public function acceptchallengeAction()
    {
        $gameId = (int) $this->getRequest()->getParam('id');

        try
        {
            $this->_user->acceptChallenge($gameId);
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }
        $this->_makeFbAppRequestAcceptChallenge($gameId);
    }

    /**
     * makes an fb app request through fb api
     *
     * @todo move this func. to an action helper or etc.
     *
     * @param int $gameId
     */
    private function _makeFbAppRequestAcceptChallenge($gameId)
    {
        $game = new YBCore_Model_Event_Game_Challenge($gameId);
        $targetUserList = $game->getHomeTeam()->getRoster();

        $message =$this->view->textHelper()->getAcceptChallengeAppRequestText($this->_user);

        try {
            foreach ($targetUserList as $targetUser){
                $this->_facebook->makeAppRequest($targetUser->getFbId(),$message);
            }
        } catch (FacebookApiException $e) {
        }
    }

    public function withdrawchallengeAction()
    {
        $gameId = (int) $this->getRequest()->getParam('id');

        try
        {
            $this->_user->withdrawChallenge($gameId);
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }
    }

    public function createchallengeAction()
    {
        try
        {
            $this->_user->createChallenge();
        } catch (YBCore_Exception_Abstract $e)
        {
            //echo ($e->getMessage());
            //die();
        }
        $this->_makeFbAppRequestCreateChallenge();
    }

    /**
     * makes an fb app request through fb api
     *
     * @todo move this func. to an action helper or etc.
     *
     */
    private function _makeFbAppRequestCreateChallenge()
    {
        $teamOwnerMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_TeamOwner');
        $ybTeamOwnerList = $teamOwnerMapper->loadUserList();

        $message = $this->view->textHelper()->getCreateChallengeAppRequestText($this->_user);
        try {
            foreach ($ybTeamOwnerList as $teamOwner) {
                if($this->_user->getId() !== $teamOwner->getId()){
                    $this->_facebook->makeAppRequest($teamOwner->getFbId(),$message);
                }
            }
        } catch (FacebookApiException $e) {
        }
    }
}