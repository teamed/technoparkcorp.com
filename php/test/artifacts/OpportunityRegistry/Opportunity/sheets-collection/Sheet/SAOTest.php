<?php
/**
 * @version $Id: ROMTest.php 818 2010-03-02 13:50:37Z yegor256@yahoo.com $
 */

require_once 'AbstractTest.php';

class Sheet_SAOTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        $opp = $registry->current();
        $this->_sao = $opp->sheets['SAO'];
    }
    
    public function testTexIsRenderable()
    {
        $tex = $this->_sao->getDiagram();
        $this->assertTrue(strlen($tex) > 0);
        // logg($tex);
    }

}