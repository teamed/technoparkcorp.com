<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';
require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Vision/Diagram.php';

class Sheet_ScheduleEstimate_ChartTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();
        $registry = Model_Artifact::root()->opportunityRegistry;
        $registry->reload();
        $this->_opp = $registry->current();

        $this->_chart = $chart = new Sheet_ScheduleEstimate_Chart();
        $chart->addBar('deposit', 0, '$1500 to be paid to our bank account');
        $chart->addBar('analysis', 15, 'we analyze the requirements provided', 1.2);
        $chart->addBar('second payment', 0, '$5000 more to the same account', 1.5);
        $chart->addBar('coding', 45, 'we implement the code', 1.8);
        
        $chart->addDependency('deposit', 'analysis');
        $chart->addDependency('analysis', 'second payment', Sheet_ScheduleEstimate_Chart::DEP_FS, 3);
        $chart->addDependency('second payment', 'coding', Sheet_ScheduleEstimate_Chart::DEP_FS, 5);
    }
    
    public function testDiagramIsConvertableToLatex()
    {
        $tex = $this->_chart->getLatex($this->_opp->sheets->getView());
        $this->assertTrue(is_string($tex));
        logg($tex);
    }

}