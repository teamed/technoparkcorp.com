<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Sheet_SheetAbstractTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        $this->_opp = $registry->current();
    }
    
    public function testFactoryMethodWorks()
    {
        $this->assertTrue(Sheet_Abstract::isValidName('Vision'));
    }

    public function testGatewayToXmlWorks()
    {
        $this->assertTrue(is_string($this->_opp->sheets['Vision']->product));
    }

}