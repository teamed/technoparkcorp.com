<?php

require_once 'FaZend/Test/TestCase.php';

class Decision_HistoryTest extends FaZend_Test_TestCase 
{

    public function testNewHistoryRecordsCanBeRetrieved() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        Model_Decision_History::retrieveByWobot($wobot);
        Model_Decision_History::retrieveByWobotNonEmpty($wobot);
    }
    
    public function testHistoryRecordsCanBeDeleted() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        Model_Decision_History::cleanByWobot($wobot);
    }
    
}