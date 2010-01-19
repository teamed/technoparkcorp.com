<?php

require_once 'FaZend/Test/TestCase.php';

class WobotTest extends FaZend_Test_TestCase 
{

    public function testRetrieveAllWorks() 
    {
        $wobots = Model_Wobot::retrieveAll();
        $this->assertTrue(count($wobots) > 0, 'No wobots, why?');
    }

    public function testFactoryMethodWorks() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        
        $this->assertEquals('PM', $wobot->getName(), 'Wobot name is not PM, why?');
        $this->assertEquals(Mocks_Model_Project::NAME, $wobot->getContext(), 'Context is not equal to project name, why?');
    }

    public function testWobotCanBeExecuted() 
    {
        $wobot = Model_Wobot::factory('PM.' . Mocks_Model_Project::NAME);
        $wobot->execute();
    }
    
}