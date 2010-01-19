<?php

require_once 'FaZend/Test/TestCase.php';

class theProjectRegistryTest extends FaZend_Test_TestCase
{

    public function testCollectionOfProjectsWorks() 
    {
        $registry = Model_Artifact::root()->projectRegistry;
        $count = 0;
        foreach ($registry as $project) {
            $count++;
        }
        $this->assertTrue($count > 0, 'No projects found, why?');
    }

    public function testGetStaffRequestsWorks() 
    {
        $requests = Model_Artifact::root()->projectRegistry->getStaffRequests();
    }
}