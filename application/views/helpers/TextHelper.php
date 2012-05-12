<?php
/**
 * Helper to do text translations from model data to human knowledge :)
 */
class Zend_View_Helper_TextHelper
{

    /**
     * Returns reference to self
     */
    public function textHelper()
    {
        return $this;
    }

    /**
     * 
     * Converts digit to word
     * @param int $digit
     */
    function convertDigitToWord($digit)
    {
        switch ($digit)
        {
            case "1":
                return "one";
            case "2":
                return "two";
            case "3":
                return "three";
            case "4":
                return "four";
            case "5":
                return "five";
            case "6":
                return "six";
            case "7":
                return "seven";
            case "8":
                return "eight";
            case "9":
                return "nine";
        }
    }

    /**
     * Returns the text data of the remove connection button
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $observedModel
     * @param YBCore_Model_Connection_Abstract $connection
     * @param bool $isExecutingRequest
     */
    public function getRemoveConnectionButtonText(YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $observedModel, YBCore_Model_Connection_Abstract $connection, $isExecutingRequest)
    {
        $textDataListCancel = array('short' => 'Cancel', 'long' => 'Cancel Contract', 'header' => 'Are you sure you want to cancel this contract?');
        $textDataListExecuteCancelRequest = array('short' => 'Approve', 'long' => 'Cancel Contract', 'header' => 'Are you sure you want to cancel the contract of this player?');
        $textDataListRefuse = array('short' => 'Refuse', 'long' => 'Refuse Offer', 'header' => 'Are you sure you want to refuse this offer?');
        $textDataListWithdraw = array('short' => 'Withdraw', 'long' => 'Withdraw Offer', 'header' => 'Are you sure you want to withdraw this offer?');
        $textDataListRejectTransferRequest = array('short' => 'Reject', 'long' => 'Reject Transfer Request', 'header' => 'Are you sure you want to reject this transfer request?');
        $textDataListRejectDismissRequest = array('short' => 'Reject', 'long' => 'Reject Dissmiss Request', 'header' => 'Are you sure you want to reject this dismiss request?');
        $textDataList = null;
        
        if (true === $isExecutingRequest)
            return $textDataListExecuteCancelRequest;
        
        if ($observedModel instanceof YBCore_Model_User_Abstract)
        {
            if ($loginUser instanceof YBCore_Model_User_Player)
                switch ($connection->getStatus())
                {
                    case YBCore_Model_Connection_Mapper_Contract::STATUS:
                        $textDataList = $textDataListCancel;
                        break;
                    case YBCore_Model_Connection_Mapper_Offer::STATUS:
                        switch ($connection->getType())
                        {
                            case YBCore_Model_Connection_Mapper_Connection::TYPE_TEAM:
                                $textDataList = $textDataListRefuse;
                                break;
                            case YBCore_Model_Connection_Mapper_Connection::TYPE_INDIVIDUAL:
                                $textDataList = $textDataListWithdraw;
                                break;
                        }
                        break;
                }
            
            if ($loginUser instanceof YBCore_Model_User_Coach)
                switch ($connection->getStatus())
                {
                    case YBCore_Model_Connection_Mapper_Contract::STATUS:
                        $textDataList = $textDataListCancel;
                        break;
                    case YBCore_Model_Connection_Mapper_Offer::STATUS:
                        $textDataList = $textDataListRefuse;
                        break;
                }
        } elseif ($observedModel instanceof YBCore_Model_Team)
        {
            if ($loginUser instanceof YBCore_Model_User_TeamOwner)
                switch ($connection->getStatus())
                {
                    case YBCore_Model_Connection_Mapper_Contract::STATUS:
                        $textDataList = $textDataListCancel;
                        break;
                    case YBCore_Model_Connection_Mapper_Offer::STATUS:
                        $textDataList = $textDataListWithdraw;
                        break;
                    case YBCore_Model_Connection_Mapper_Request::STATUS:
                        if (true === in_array($connection->getEmployeeId(), $observedModel->getEmployeeIdList(), true))
                            $textDataList = $textDataListRejectDismissRequest;
                        else
                            $textDataList = $textDataListRejectTransferRequest;
                        break;
                }
            
            if ($loginUser instanceof YBCore_Model_User_Coach && YBCore_Model_Connection_Mapper_Request::STATUS === $connection->getStatus())
                $textDataList = $textDataListWithdraw;
        }
        return $textDataList;
    }

    /**
     * Returns the text data of the modify connection button
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $observedModel
     * @param YBCore_Model_Connection_Abstract $connection
     */
    public function getModifyConnectionButtonText(YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $observedModel, YBCore_Model_Connection_Abstract $connection)
    {
        $textDataListAccept = array('short' => 'Accept', 'long' => 'Accept Offer', 'header' => 'Are you sure you want to accept this offer?');
        return $textDataListAccept;
    }

    /**
     * Returns the text data of the create connection button
     * 
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $targetUser
     * @param bool $isExecutingRequest
     */
    public function getCreateConnectionButtonText(YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $targetUser, $isExecutingRequest)
    {
        $buttonText = null;
        $title = null;
        $header = null;
        $isRequest = false;
        $disableDayList = true;
        $contractDayList = array();
        
        if ($loginUser instanceof YBCore_Model_User_Player && $targetUser instanceof YBCore_Model_User_Coach)
        {
            $disableDayList = false;
            $buttonText = 'Hire';
            $title = 'Hire Trainer';
            $header = 'Are you sure you want to hire this trainer?';
        } elseif (true === $loginUser instanceof YBCore_Model_User_Coach && true === $targetUser instanceof YBCore_Model_User_Player)
        {
            $isRequest = true;
            if (in_array($targetUser->getId(), $loginUser->getTeam()->getEmployeeIdList()))
            {
                $buttonText = 'Dismiss';
                $title = 'Dismiss Player';
                $header = 'Are you sure you want to dismiss this player?';
            } else
            {
                $buttonText = 'Transfer';
                $title = 'Transfer Player';
                $header = 'Are you sure you want to transfer this player?';
            }
        } elseif ($loginUser instanceof YBCore_Model_User_TeamOwner)
        {
            $team = $loginUser->getTeam();
            $coachList = $team->getCoachList();
            if (true === $targetUser instanceof YBCore_Model_User_Coach)
            {
                $disableDayList = false;
                $title = 'Transfer Coach';
                $header = 'Are you sure you want to transfer this coach?';
            } else
            {
                $title = 'Transfer Player';
                $header = 'Are you sure you want to transfer this player?';
            }
            
            $contractList = $team->getContractList();
            if (false === empty($contractList))
                foreach ($contractList[0]->getDayList() as $day)
                    $contractDayList[] = $day->getDayId();
            $buttonText = 'Transfer';
        }
        
        if (true === $isExecutingRequest)
        {
            $buttonText = 'Approve';
        }
        
        $dataList = array('short' => $buttonText, 'long' => $title, 'header' => $header, 'isRequest' => $isRequest, 'disableDayList' => $disableDayList, 'contractDayList' => $contractDayList);
        return $dataList;
    }

    /**
     * Returns the title of the given user
     * 
     * @param YBCore_Model_User_Abstract $user
     * @return string
     */
    public function getUserTitle($user)
    {
        if ($user instanceof YBCore_Model_User_TeamOwner)
            return 'Team Owner';
        if ($user instanceof YBCore_Model_User_Coach)
            return 'Coach';
        if ($user instanceof YBCore_Model_User_Player)
            switch ($user->getPosition())
            {
                case YBCore_Model_User_Mapper_Player::POSITION_GUARD:
                    return 'Guard';
                case YBCore_Model_User_Mapper_Player::POSITION_FORWARD:
                    return 'Forward';
                case YBCore_Model_User_Mapper_Player::POSITION_CENTER:
                    return 'Center';
            }
    }

    /**
     * Returns the text of the game list link
     *
     * @return string
     */
    public function getGameListLinkText()
    {
        return (YBCore_Utility_DateTime::hasGameTimePassed() ? 'Tomorrow\'s' : 'Today\'s') . ' Games';
    }

    /**
     * Returns the title text and the css className of the given event 
     *
     * @param YBCore_Model_Event_Abstract $event
     * @return array of string (title,cssClass)
     */
    public function getEventTextDataList(YBCore_Model_Event_Abstract $event)
    {
        if ($event instanceof YBCore_Model_Event_Game_Abstract)
        {
            $eventTitleClassName = "game";
            $eventTitle = "Game";
        } elseif ($event instanceof YBCore_Model_Event_Training)
        {
            switch ($event->getType())
            {
                case YBCore_Model_Event_Training::TYPE_INDIVIDUALTRAINING:
                    $eventTitleClassName = "individualTraining";
                    $eventTitle = "Private Training";
                    break;
                case YBCore_Model_Event_Training::TYPE_TEAMTRAINING:
                    $eventTitleClassName = "teamTraining";
                    $eventTitle = "Team Training";
                    break;
            }
        }
        return array('title' => $eventTitle, 'cssClass' => $eventTitleClassName);
    }

    /**
     * Returns the css class name of the event day in the weekView template 
     *
     * @param int $dayId
     * @param bool $isFreeDay
     * @return string 
     */
    public function getWeekViewEventDayClassName($dayId, $isFreeDay)
    {
        $todayId = YBCore_Utility_DateTime::getCurrentDayId();
        $eventDayClassName = $todayId === $dayId ? "today " : "";
        $eventDayClassName .= true === $isFreeDay ? "freeDay" : "";
        return $eventDayClassName;
    }

    /**
     * Returns the empty schedule alert message for the given user 
     *
     * @param YBCore_Model_User_Abstract $user
     * @return string 
     */
    public function getEmptyScheduleAlertMessage(YBCore_Model_User_Abstract $user)
    {
        $hasTeam = null !== $user->getTeamId();
        
        $message = "<b>Your schedule is empty!</b> Don't you have things to do?<br/>";
        if ($user instanceof YBCore_Model_User_Player)
        {
            $individualContractorIdList = $user->getIndividualContractorIdList();
            $hasTrainer = false === empty($individualContractorIdList);
            
            $message .= false === $hasTeam ? "You dont have a team yet, go to <b>League</b> above and find your self a team." : "Tell your team owner to schedule some games and trainings.";
            $message .= "<br/>";
            $message .= false === $hasTrainer ? "You dont have any trainers, hire a coach and start training to be the best " . $this->getUserTitle($user) . " in the league" : "";
        } elseif ($user instanceof YBCore_Model_User_Coach)
        {
            $individualContractorIdList = $user->getIndividualContractorIdList();
            $hasIndividualContractor = false === empty($individualContractorIdList);
            
            $message .= false === $hasTeam ? "You dont have a team yet, go to <b>League</b> above and find your self a team." : "Tell your team owner to schedule some games and trainings.";
            $message .= false === $hasIndividualContractor ? "<br/>You can give private trainings for some extra cash. Go to <b>Members</b> above and find some players to train." : "";
        } elseif ($user instanceof YBCore_Model_User_TeamOwner)
        {
            $teamName = $user->getTeam()->getName();
            $hasEnoughPlayers = 4 < count($user->getTeam()->getEmployeeIdList());
            
            $message .= false === $hasEnoughPlayers ? "Your team is lacking players. Go to <b>Members</b> above and transfer some players to " . $teamName . "." : "";
            $message .= "<br/>";
            $message .= "You dont have any games scheduled. Go to <b>League</b> above and accept a challenge or create one. Don't forget playing games are essential to make revenue.";
        }
        return $message;
    }

    /**
     * Returns the empty connectionList alert message for the given user 
     *
     * @param YBCore_Model_User_Abstract $user
     * @return string 
     */
    public function getEmptyConnectionListMessage(YBCore_Model_User_Abstract $user)
    {
        if ($user instanceof YBCore_Model_User_TeamOwner)
        {
            $message = "<b>Your team doesn't have any players!</b><br/>";
            $message .= "Go to <b>League</b> above and transfer some players to your team.";
        } else
        {
            $message = "<b>You don't have any contracts!</b><br/>";
            $message .= "Go to <b>League</b> above and start making connections.";
        }
        return $message;
    }

    /**
     * Returns the title of connection view 
     *
     * @param int $connectionStatus YBCore_Model_Connection_Mapper_*::STATUS
     * @return string 
     */
    public function getConnectionTitle($connectionStatus)
    {
        $title = null;
        switch ($connectionStatus)
        {
            case YBCore_Model_Connection_Mapper_Contract::STATUS:
                $title = "Contracts";
                break;
            case YBCore_Model_Connection_Mapper_Offer::STATUS:
                $title = "Offers";
                break;
            case YBCore_Model_Connection_Mapper_Request::STATUS:
                $title = "Requests";
                break;
        }
        return $title;
    }

    /**
     * Returns the description for the given connection 
     *
     * @param YBCore_Model_Connection_Abstract $connection
     * @param YBCore_Model_User_Abstract $loginUser
     * @param YBCore_Interface_Connectable $observedModel
     * @param string $observedModelTitleIndicator
     * @param string $participantTitleIndicator
     * @return string 
     */
    public function getConnectionDescription(YBCore_Model_Connection_Abstract $connection, YBCore_Model_User_Abstract $loginUser, YBCore_Interface_Connectable $observedModel, 
    $observedModelTitleIndicator, $participantTitleIndicator)
    {
        $dayList = $connection->getDayList();
        $dayListCount = count($dayList);
        
        if (0 === $dayListCount && YBCore_Model_Connection_Mapper_Connection::TYPE_TEAM === $connection->getType())
        {
            $teamTitleIndicator = true === $observedModel instanceof YBCore_Model_Team ? $observedModelTitleIndicator : $participantTitleIndicator;
            return $teamTitleIndicator . ' has not scheduled any trainings yet.';
        }
        
        $description = null;
        $verb = null;
        switch ($connection->getStatus())
        {
            case YBCore_Model_Connection_Mapper_Contract::STATUS:
                $verb = " paying ";
                break;
            case YBCore_Model_Connection_Mapper_Offer::STATUS:
                $verb = " offering ";
                break;
            case YBCore_Model_Connection_Mapper_Request::STATUS:
                break;
        }
        
        if (true === $observedModel instanceof YBCore_Model_Team)
        {
            $employerIndicator = $observedModelTitleIndicator . ' is';
            $employeeIndicator = $loginUser->getId() === $connection->getEmployeeId() ? 'you' : $participantTitleIndicator;
        } elseif (true === $observedModel instanceof YBCore_Model_User_Abstract)
        {
            if ($observedModel->getId() === $connection->getEmployerId() && YBCore_Model_Connection_Mapper_Connection::TYPE_INDIVIDUAL === $connection->getType())
            {
                $employerIndicator = $loginUser->getId() === $observedModel->getId() ? 'You are' : $observedModelTitleIndicator . ' is';
                $employeeIndicator = $loginUser->getId() === $connection->getEmployeeId() ? 'you' : $participantTitleIndicator;
            } else
            {
                $employerIndicator = $loginUser->getId() === $connection->getEmployerId() && YBCore_Model_Connection_Mapper_Connection::TYPE_INDIVIDUAL === $connection->getType() ? 'You are' : $participantTitleIndicator .
                 ' is';
                $employeeIndicator = $loginUser->getId() === $connection->getEmployeeId() ? 'you' : $observedModelTitleIndicator;
            }
        }
        
        $description = $employerIndicator . $verb . $employeeIndicator . ' <b>' . $connection->getSalary() * count($connection->getDayList()) . '$ for ' .
         $this->convertDigitToWord(count($connection->getDayList())) . ' training' . (1 < count($connection->getDayList()) ? 's' : '') . '</b> per week.';
        
        $description .= " Training" . (1 < count($connection->getDayList()) ? 's are ' : ' is ') . 'on';
        
        $dayList = $connection->getDayList();
        $dayListCount = count($dayList);
        for ($i = 0; $i < $dayListCount; $i++)
        {
            $connectionDay = $dayList[$i];
            $description .= ' <b>' . YBCore_Utility_StringFormatter::getDayName($connectionDay->getDayId()) . '</b>';
            switch ($i)
            {
                case $dayListCount - 1: // last one 
                    $description .= '';
                    break;
                case $dayListCount - 2: // one before last
                    $description .= ' and';
                    break;
                default:
                    $description .= ',';
                    break;
            }
        }
        $description .= ' at ' . YBCore_Utility_DateTime::$trainingTime . '.';
        return $description;
    }

    /**
     * Returns the css Class of the given connection&Model 
     *
     * @param YBCore_Model_Connection_Abstract $connection
     * @param YBCore_Interface_Connectable $observedModel
     * @return string 
     */
    public function getConnectionRowCssClass(YBCore_Model_Connection_Abstract $connection, YBCore_Interface_Connectable $observedModel)
    {
        if (false === $connection instanceof YBCore_Model_Connection_Contract)
            return;
        
        return true === $observedModel->isExpense($connection) ? "expense" : "income";
    }

    /**
     * Returns the NoData text for connectionListView 
     *
     * @param string $observedUserIndicator
     * @param int $connectionStatus YBCore_Model_Connection_Mapper_*::STATUS:
     * @return string 
     */
    public function getConnectionListNoDataText($observedUserIndicator, $connectionStatus)
    {
        $connectionTitle = null;
        switch ($connectionStatus)
        {
            case YBCore_Model_Connection_Mapper_Contract::STATUS:
                $connectionTitle = "contract";
                break;
            case YBCore_Model_Connection_Mapper_Offer::STATUS:
                $connectionTitle = "offer";
                break;
            case YBCore_Model_Connection_Mapper_Request::STATUS:
                $connectionTitle = "request";
                break;
        }
        $noDataText = null === $observedUserIndicator ? "You don't " : $observedUserIndicator . " doesn't ";
        $noDataText .= "have any " . $connectionTitle . "s at the moment.";
        return $noDataText;
    }

    /**
     * Returns the NoData text for requestListView 
     *
     * @param string $observedTeamIndicator
     * @param string $type type of the request
     * @return string 
     */
    public function getRequestListNoDataText($observedTeamIndicator, $type)
    {
        $noDataText = null === $observedTeamIndicator ? "You don't " : $observedTeamIndicator . " doesn't ";
        $noDataText .= "have any " . $type . " requests at the moment.";
        return $noDataText;
    }

    /**
     * Returns informationText of the requestListView
     *
     * @param string $title
     * @param string $ownerIndicator teamOwner
     * @param string $coachIndicator headCoach
     * @param bool $isPlural
     * @return string 
     */
    public function getRequestListInformation($title, $ownerIndicator, $coachIndicator, $isPlural)
    {
        $verb = null;
        if ("transfer list" === strtolower($title))
            $verb = "transfer";
        else
            $verb = "dismiss";
        
        $message = null === $coachIndicator ? 'You have' : $coachIndicator . ' has';
        $message .= ' requested ';
        $message .= null === $ownerIndicator ? 'you' : $ownerIndicator;
        $message .= ' to ' . $verb . ' the player' . (true === $isPlural ? 's listed' : '') . ' below.';
        
        if (null === $coachIndicator)
        {
            $message .= '<br/>';
            $message .= '<div class="helperLine">You can withdraw your request' . (true === $isPlural ? 's' : '') . '.</div>';
        }
        
        if (null === $ownerIndicator)
        {
            $message .= '<br/>';
            $message .= '<div class="helperLine">You can reject ' . (true === $isPlural ? 'these' : 'this') . ' request' . (true === $isPlural ? 's' : '');
            $message .= ' or you can approve and ' . $verb . ' the player' . (true === $isPlural ? 's' : '') . '.</div>';
        }
        return $message;
    }

    /**
     * Returns informationText of the requestListView
     *
     * @param [optional] YBCore_Model_User_Abstract $loginUser = null
     * @return string 
     */
    public function getGameViewInfoText(YBCore_Model_User_Abstract $loginUser = null)
    {
        $text = 'Next game time is ' . (YBCore_Utility_DateTime::hasGameTimePassed() ? 'tomorrow' : 'today') . ' at ' . YBCore_Utility_DateTime::$gameTime;
        if (null !== $loginUser && true === $loginUser instanceof YBCore_Model_User_TeamOwner)
            $text .= '<div class="helperLine">You can schedule a game at this time by accepting a challenge.</div>';
        return $text;
    }
    
    /**
    * Returns fb app request message
    *
    * @param YBCore_Model_User_Abstract $loginUser
    * @return string
    */
    public function getModifyConnectionAppRequestText(YBCore_Model_User_Abstract $loginUser)
    {
    	return $loginUser->getFbName(). " has accepted your contract. Visit your team to see the new roster.";
    }
    
    /**
    * Returns fb app request message
    *
    * @param YBCore_Model_User_Abstract $loginUser
    * @return string
    */
    public function getAcceptChallengeAppRequestText(YBCore_Model_User_Abstract $loginUser)
    {
    	return $loginUser->getFbName(). " has accepted your team's challenge. Check your schedule.";
    }
    
    /**
    * Returns fb app request message
    *
    * @param YBCore_Model_User_Abstract $loginUser
    * @return string
    */
    public function getCreateConnectionAppRequestText(YBCore_Model_User_Abstract $loginUser)
    {
        return $loginUser->getFbName(). " has just sent you a new contract offer. Check your contracts.";
    }
    
    /**
    * Returns fb app request message
    *
    * @param YBCore_Model_User_Abstract $loginUser
    * @return string
    */
    public function getCreateChallengeAppRequestText(YBCore_Model_User_Abstract $loginUser)
    {
    	return $loginUser->getFbName(). " has created a new challenge. Go to challenge wall to accept it.";
    }
}