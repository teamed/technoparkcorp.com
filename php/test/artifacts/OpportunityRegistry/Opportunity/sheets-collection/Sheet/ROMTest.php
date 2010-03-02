<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Sheet_ROMTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        $opp = $registry->current();
        $this->_rom = $opp->sheets['ROM'];
    }
    
    public function testBasicPropertiesAreAccessible()
    {
        $this->assertTrue(
            is_integer($this->_rom->hours), 
            'HOURS are wrong: ' . $this->_rom->hours
        );
    }

    public function testClassIsSerializable()
    {
        $serialized = serialize($this->_rom);
        $rom2 = unserialize($serialized);
        $this->assertEquals($this->_rom->hours, $rom2->hours);
    }

    public function testHoursAreMathAverageOfEstimations()
    {
        $hours = array();
        foreach ($this->_rom->estimates as $estimate) {
            $hours[] = $estimate->hours;
        }
        $avg = array_sum($hours) / count($hours);
        $this->assertEquals($avg, $this->_rom->hours);
    }

}