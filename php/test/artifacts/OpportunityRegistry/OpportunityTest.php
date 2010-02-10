<?php

require_once 'AbstractTest.php';

class theOpportunityTest extends AbstractTest
{

    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        $this->_opp = $registry->current();
    }
    
    public function testOpportunityIsConvertableToLatex()
    {
        $tex = $this->_opp->getLatex();
        $this->assertTrue(strlen($tex) > 0, 'Empty TeX, why?');
    }

}