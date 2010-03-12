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
    
    public function testChartIsRenderable()
    {
        $tex = $this->_schedule->getChart();
        $this->assertFalse(empty($tex), "Empty chart, why?");
        logg($tex);
    }

}