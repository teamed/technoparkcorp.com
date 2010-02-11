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
        $this->assertTrue(is_string($this->_opp->sheets->dump()));
        // logg($this->_opp->sheets->dump());
        // logg($tex);
    }

    public function testWeCanSendOpportunityByEmail()
    {
        $this->_opp->sendByEmail('test@example.com');
    }

    public function testOpportunityIsRecoverableInPos()
    {
        $registry = Model_Artifact::root()->opportunityRegistry;
        $this->_opp = $registry->current();
    }

}