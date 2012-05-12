<?php
// @todo move to YBCore tree
// @todo add date time checks, not allow to do twice in a day

define(ROSTERCOUNT, 'rosterCount');
define(SALARYTOTAL, 'salaryTotal');

require_once (dirname(__DIR__).'/cronBootstrap.php');
require_once ('function.php');

echo "bootstrap completed";

$gameTable = new YBCore_Model_Event_Game_DbTable_Game();
$teamTable = new YBCore_Model_DbTable_Team();
$contractTable = new YBCore_Model_Connection_DbTable_Connection();

$gameTable->getAdapter()->beginTransaction();
try
{
    $gameStatsDataList = array();

    $select = $gameTable->select()->where('status = ?', 2);

    $rowSet = $gameTable->fetchAll($select);

    if (0 !== $rowSet->count()){

        foreach ($rowSet as $row)
        {
            $gameStatsDataList[$row->id][$row->home][ROSTERCOUNT]= 0;
            $gameStatsDataList[$row->id][$row->home][SALARYTOTAL]= 0;
            $gameStatsDataList[$row->id][$row->away][ROSTERCOUNT]= 0;
            $gameStatsDataList[$row->id][$row->away][SALARYTOTAL]= 0;
        }

        $select = $gameTable->select()
        ->setIntegrityCheck(false)
        ->from(array("g" => $gameTable->info(Zend_Db_Table::NAME)), array('gameId' => 'id', Zend_Db_Select::SQL_WILDCARD))
        ->join(array("t" => $teamTable->info(Zend_Db_Table::NAME)), 't.id = g.home OR t.id = g.away', array('teamId' => 'id', Zend_Db_Select::SQL_WILDCARD))
        ->joinLeft(array("c" => $contractTable->info(Zend_Db_Table::NAME)), 'c.employer = t.id')
        ->where('g.status = ?', 2)
        ->where('c.status = ?', 1);

        if ('debug' === $argv[1])
        {
            echo $select;
            die();
        }

        $rowSet = $gameTable->fetchAll($select);

        if (0 !== $rowSet->count())
        {
            foreach ($rowSet as $row)
            {
                $gameStatsDataList[$row->gameId][$row->teamId][ROSTERCOUNT]++;
                $gameStatsDataList[$row->gameId][$row->teamId][SALARYTOTAL] += $row->salary;
            }
        }

        updateSelectedGameList($gameStatsDataList);
        insertNewGameStats($gameStatsDataList);

        $gameTable->getAdapter()->commit();
        echo "committed";
    }
} catch (Exception $e)
{
    echo $e->getMessage();
    $gameTable->getAdapter()->rollBack();
    echo "rolled backed";
    return;
}

insertGameCronJobLog();