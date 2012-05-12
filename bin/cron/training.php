<?php

// @todo move to YBCore tree
// @todo implement archive for trainings

require_once (dirname(__DIR__).'/cronBootstrap.php');
require_once ('function.php');

echo "bootstrap completed";

$today = date('N', strtotime(date("Y-m-d"))) - 1;

$connectionTable = new YBCore_Model_Connection_DbTable_Connection();
$connectionDayTable = new YBCore_Model_Connection_DbTable_Day();
$teamTable = new YBCore_Model_DbTable_Team();
$userTable = new YBCore_Model_User_DbTable_User();

$connectionTable->getAdapter()->beginTransaction();
try
{
    $select = $connectionTable->select()
        ->setIntegrityCheck(false)
        ->from(array("c" => $connectionTable->info(Zend_Db_Table::NAME)), array('connectionId' => 'id', Zend_Db_Select::SQL_WILDCARD))
        ->join(array("cd" => $connectionDayTable->info(Zend_Db_Table::NAME)), 'c.id = cd.connection', array('connectionDayId' => 'id', Zend_Db_Select::SQL_WILDCARD))
        ->joinLeft(array("t" => $teamTable->info(Zend_Db_Table::NAME)), 'c.employer = t.id', array('teamId' => 'id'))
        ->joinLeft(array("o" => $userTable->info(Zend_Db_Table::NAME)), 't.owner = o.id', array('ownerId' => 'id', 'ownerBalance' => 'balance'))
        ->joinleft(array("ur" => $userTable->info(Zend_Db_Table::NAME)), 'c.employer = ur.id', array('userEmployerId' => 'id', 'userEmployerBalance' => 'balance'))
        ->join(array("ue" => $userTable->info(Zend_Db_Table::NAME)), 'c.employee = ue.id', array('userEmployeeId' => 'id', 'userEmployeeBalance' => 'balance'))
        ->where('c.status = ?', 1)
        ->where('cd.day = ?', $today);
    
    if ('debug' === $argv[1])
    {
        echo $select;
        die();
    }
    
    $newBalance = null;
    $userId = null;
    $rowSet = $connectionTable->fetchAll($select);
    foreach ($rowSet as $row)
    {
        if (1 === $row->type)
        {
            $employerNewBalance = $row->ownerBalance - $row->salary;
            $employerId = $row->ownerId;
        } else
        {
            $employerNewBalance = $row->userEmployerBalance - $row->salary;
            $employerId = $row->userEmployerId;
        }
        
        updateUserBalance($employerId, $employerNewBalance);
        
        $employeeNewBalance = $row->userEmployeeBalance + $row->salary;
        $employeeId = $row->userEmployeeId;
        updateUserBalance($employeeId, $employeeNewBalance);
    }
    $connectionTable->getAdapter()->commit();
    echo "committed";
    
} catch (Exception $e)
{
    echo $e->getMessage();
    $connectionTable->getAdapter()->rollback();
    echo "rolled back";
    return;
}

insertTrainingCronJobLog(); 