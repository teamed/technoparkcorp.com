<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Decision_HistoryTest extends AbstractTest 
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