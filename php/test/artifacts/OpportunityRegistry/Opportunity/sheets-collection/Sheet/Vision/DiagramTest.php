<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';
require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Vision/Diagram.php';

class DiagramFoo extends Sheet_Vision_Diagram
{
    public function getBestCells($angle)
    {
        return $this->_getBestCells($angle);
    }   
}

class Sheet_Vision_DiagramTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        $registry->reload();
        $this->_opp = $registry->current();

        $this->_diagram = $d = new Sheet_Vision_Diagram();
        $d->addFeature('Login', 'User');
        $d->addFeature('Register New Account', 'User');
        $d->addFeature('Print Summary Reports', 'Site Administrator');
        $d->addFeature('Send Massive Emails to System Users', 'Site Administrator');
    }
    
    public function testDiagramIsConvertableToLatex()
    {
        $this->assertTrue(is_string($this->_diagram->getLatex($this->_opp->sheets->getView())));
    }

    public static function providerCoordinates()
    {
        return array(
            array(1/4, 3, 3),
            array(1/2, 2, 3),
            array(3/4, 0, 3),
            array(1, 0, 1),
        );
    }

    /**
     * @dataProvider providerCoordinates
     */
    public function testBestCellsAreFoundProperly($angle, $needX, $needY)
    {
        $foo = new DiagramFoo();
        $coordinates = $foo->getBestCells(pi() * $angle); // right top
        list($x, $y, $distance) = array_shift($coordinates);
        
        $this->assertEquals($x, $needX, 'Invalid X');
        $this->assertEquals($y, $needY, 'Invalid Y');
    }

}