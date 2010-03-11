<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Sheet_ScheduleEstimateTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // reload it explicitly
        $registry->reload();
        $opp = $registry->current();
        $this->_schedule = $opp->sheets['ScheduleEstimate'];
    }
    
    public function testBasicPropertiesAreAccessible()
    {
        // $this->assertTrue(
        //     is_integer($this->_rom->hours), 
        //     'HOURS are wrong: ' . $this->_rom->hours
        // );
    }

    public function testClassIsSerializable()
    {
        $serialized = serialize($this->_schedule);
        $schedule2 = unserialize($serialized);
        $this->assertEquals($this->_schedule->getChart(), $schedule2->getChart());
    }

    public function testChartIsRenderable()
    {
        $chart = $this->_schedule->getChart();
        $this->assertFalse(empty($chart), "Empty chart, why?");
    }

}