<?php

require_once 'FaZend/Test/TestCase.php';

class PMDecisionsTest extends FaZend_Test_TestCase 
{

    public function testDecisionsCanBeMade() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        
        foreach (Model_Decision::getDecisionFiles($wobot) as $file) {
            $decision = $wobot->decisionFactory($file);
            $decision->make();
        }
    }
    
}