<?php

require_once 'AbstractTest.php';

class theOpportunityRegistryTest extends AbstractTest
{

    public function testCollectionOfOppsWorks() 
    {
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        
        $count = 0;
        foreach ($registry as $opp) {
            $count++;
            $this->assertTrue(strlen($opp->id) > 0, 'ID is empty, why?');
        }
        $this->assertTrue($count > 0, 'No opps found, why?');
    }

}