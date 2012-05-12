<?php

/**
 * Updates the statuses (from scheduled to completed) of the selected games'
 * 
 * @param array $gameStatsData
 */
function updateSelectedGameList($gameStatsData)
{
    $gameIdList = array();
    foreach ($gameStatsData as $teamId => $dataList)
        $gameIdList[] = $teamId;
    
    $gameTable = new YBCore_Model_Event_Game_DbTable_Game();
    $updateData['status'] = 1;
    $where = $gameTable->getAdapter()->quoteInto('id IN (?)', $gameIdList);
    $gameTable->update($updateData, $where);
}

/**
 * Calculates and inserts the result of the given game to game_result table
 * 
 * @todo add luck generator
 * @todo add sum off all salaries for a player to equation
 * 
 * @param array $gameStatsData
 */
function insertNewGameStats($gameStatsDataList)
{
    $resultList = array();
    foreach ($gameStatsDataList as $gameId => $gameStatsData)
    {
        $resultList = array();
        $revenue = 0;
        foreach ($gameStatsData as $teamId => $dataList)
        {
            $teamPower = $dataList[ROSTERCOUNT] * $dataList[ROSTERCOUNT] * $dataList[SALARYTOTAL];
            $resultList[] = array($teamId, $teamPower);
            $revenue += $dataList[SALARYTOTAL];
        }
        
        if ($resultList[0][1] >= $resultList[1][1])
        {
            $winnerTeamId = $resultList[0][0];
            $loserTeamId = $resultList[1][0];
        } else
        {
            $winnerTeamId = $resultList[1][0];
            $loserTeamId = $resultList[0][0];
        }
        
        $insertData['created'] = date('Y-m-d H:i:s');
        $insertData['game'] = $gameId;
        $insertData['winner'] = $winnerTeamId;
        $insertData['loser'] = $loserTeamId;
        $insertData['winner_score'] = 20;
        $insertData['loser_score'] = 0;
        $insertData['revenue'] = $revenue ? $revenue : 10;
        
        $statsTable = new YBCore_Model_Event_Game_DbTable_Result();
        $statsTable->insert($insertData);
        
        updateOwnerRevenue($winnerTeamId, round($revenue * 6 / 10));
        updateOwnerRevenue($loserTeamId, round($revenue * 4 / 10));
    }
}

/**
 * Updates the balance in user table after game
 * 
 * @param int $teamId
 * @param int $revenueShare
 */
function updateOwnerRevenue($teamId, $revenueShare)
{
    $teamTable = new YBCore_Model_DbTable_Team();
    $userTable = new YBCore_Model_User_DbTable_User();
    
    $select = $teamTable->select()
        ->setIntegrityCheck(false)
        ->from(array("t" => $teamTable->info(Zend_Db_Table::NAME)))
        ->join(array("u" => $userTable->info(Zend_Db_Table::NAME)), 't.owner = u.id')
        ->where('t.id = ?', $teamId);
    
    $row = $teamTable->fetchRow($select);
    updateUserBalance($row['owner'], (int) $row['balance'] + $revenueShare);
}

/**
 * Updates the balance in user table after training
 * 
 * @param int $userId
 * @param int $newBalance
 */
function updateUserBalance($userId, $newBalance)
{
    $userTable = new YBCore_Model_User_DbTable_User();
    $updateData['balance'] = $newBalance;
    $where = $userTable->getAdapter()->quoteInto('id = ?', $userId);
    $userTable->update($updateData, $where);
}

/**
 * Inserts Log to cronjob table
 * 
 * @param array $insertData
 */
function insertCronJobLog($insertData)
{
    $cronJobTable = new YBCore_DbTable_Cronjob();
    $insertData['time'] = date('Y-m-d H:i:s');
    $cronJobTable->insert($insertData);
}

/**
 * Inserts Log to cronjob table
 */
function insertGameCronJobLog()
{
    $insertData['description'] = 'GameCronJob';
    $insertData['result'] = true;
    insertCronJobLog($insertData);
}

/**
 * Inserts Log to cronjob table
 */
function insertTrainingCronJobLog()
{
    $insertData['description'] = 'TrainingCronJob';
    $insertData['result'] = true;
    insertCronJobLog($insertData);
}