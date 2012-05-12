<?php
require_once 'InternalController.php';

/**
 * Contains actions to show lists of connectable models
 */
class LeagueController extends InternalController
{

    public function gameAction()
    {
        $gameMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_Event_Game_Abstract');
        $gameList = $gameMapper->loadGameList(YBCore_Model_Event_Game_Mapper_ScheduledGame::STATUS);
        $this->view->gameList = $gameList;
    }

    public function challengeAction()
    {
        $gameMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_Event_Game_Abstract');
        $challengeList = $gameMapper->loadGameList(YBCore_Model_Event_Game_Mapper_Challenge::STATUS);
        $this->view->challengeList = $challengeList;
    }

    public function teamAction()
    {
        $teamMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_Team');
        $teamList = $teamMapper->loadTeamList();
        $this->view->teamList = $teamList;
    }

    public function playerAction()
    {
        $playerMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_Player');
        $playerList = $playerMapper->loadUserList();
        $this->view->playerList = $playerList;
    }

    public function coachAction()
    {
        $coachMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_Coach');
        $coachList = $coachMapper->loadUserList();
        $this->view->coachList = $coachList;
    }

    public function teamownerAction()
    {
        $ownerMapper = YBCore_Mapper_Factory::getMapper('YBCore_Model_User_TeamOwner');
        $teamOwnerList = $ownerMapper->loadUserList();
        $this->view->teamOwnerList = $teamOwnerList;
    }
}