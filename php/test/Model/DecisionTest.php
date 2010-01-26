<?php

require_once 'AbstractTest.php';

class Model_DecisionTest extends AbstractTest 
{

    public function testDecisionCanBeExecutedAndReported() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        Model_Decision_History::cleanByWobot($wobot);
        
        $file = Model_Decision::nextForWobot($wobot);
        $decision = $wobot->decisionFactory($file);
        $this->assertTrue($decision instanceof Model_Decision);
        
        $decision->make();
        
        $history = Model_Decision_History::findByHash($decision->getHash());
        $this->assertTrue($history instanceof Model_Decision_History);
        $this->assertNull($history->getPid());
        $this->assertFalse($history->isRunning());
        $this->assertFalse(Model_Decision_History::hasRunning($wobot, $decision));
        $this->assertTrue(is_bool($history->isError()));
        $this->assertTrue(is_string($history->getTitle()));
        $this->assertTrue(is_string($history->getLogFileName()));
        
        $protocol = $history->getProtocol();
        $this->assertTrue(count(explode("\n", $protocol)) > 2);
    }
    
}