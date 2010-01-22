<?php

require_once 'AbstractTest.php';

class PMDecisionsTest extends AbstractTest 
{

    public function testDecisionsCanBeMade() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        
        foreach (Model_Decision::getDecisionFiles($wobot) as $file) {
            $decision = $wobot->decisionFactory($file);
            $result = $decision->make();
            $this->assertFalse((bool)preg_match('/^error/i', $result), 
                'Failure in decision: '. $file);
        }
    }
    
}