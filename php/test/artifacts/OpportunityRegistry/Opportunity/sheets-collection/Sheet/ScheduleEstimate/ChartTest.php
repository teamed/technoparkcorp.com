<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';
require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Vision/Diagram.php';

class FooChart extends Sheet_ScheduleEstimate_Chart
{
    public function getWidth() { return $this->_calculateWidth(); }
    public function getBars() { return $this->_bars; }
}

class Sheet_ScheduleEstimate_ChartTest extends AbstractTest
{
    
    public function setUp() 
    {
        parent::setUp();

        $registry = Model_Artifact::root()->opportunityRegistry;
        $registry->reload();
        $this->_opp = $registry->current();

        $this->_chart = $chart = new FooChart();
        $chart->addBar('deposit', 0, '$1500 to be paid to our bank account');
        $chart->addBar('analysis', 15, 'we analyze the requirements provided', 1.2);
        $chart->addBar('second payment', 0, '$5000 more to the same account');
        $chart->addBar('coding', 45, 'we implement the code', 1.8);
        
        $chart->addDependency('deposit', 'analysis');
        $chart->addDependency('analysis', 'second payment', Sheet_ScheduleEstimate_Chart::DEP_FS, 3);
        $chart->addDependency('second payment', 'coding', Sheet_ScheduleEstimate_Chart::DEP_FS, 5);
    }
    
    public function testBestStartsAreCorrect()
    {
        $tex = $this->_chart->getLatex($this->_opp->sheets->getView());
        
        $bars = $this->_chart->getBars();
        $bestStarts = array(
            'deposit'        => 0,
            'analysis'       => 0,
            'second payment' => 18, // 15 + 3
            'coding'         => 23, // 15 + 3 + 5
        );
        foreach ($bars as $bar) {
            if (array_key_exists($bar['name'], $bestStarts)) {
                $this->assertEquals(
                    $bestStarts[$bar['name']],
                    $bar['bestStart'],
                    "Invalid bestStart for '{$bar['name']}'"
                );
            } else {
                $this->fail("Where this bar came from: '{$bar['name']}'");
            }
        }
    }

    public function testWorstStartsAreCorrect()
    {
        $tex = $this->_chart->getLatex($this->_opp->sheets->getView());
        $bars = $this->_chart->getBars();
        $worstStarts = array(
            'deposit'        => 0,
            'analysis'       => 0,
            'second payment' => 21, // 15*1.2 + 3
            'coding'         => 26, // 15*1.2 + 3 + 5
        );
        foreach ($bars as $bar) {
            if (array_key_exists($bar['name'], $worstStarts)) {
                $this->assertEquals(
                    $worstStarts[$bar['name']],
                    $bar['worstStart'],
                    "Invalid worstStart for '{$bar['name']}'"
                );
            } else {
                $this->fail("Where this bar came from: '{$bar['name']}'");
            }
        }
    }

    public function testWidthIsCorrect()
    {
        $tex = $this->_chart->getLatex($this->_opp->sheets->getView());
        $this->assertEquals(15*1.2 + 3 + 5 + 45*1.8, $this->_chart->getWidth());
    }

    public function testDiagramIsConvertableToLatex()
    {
        $tex = $this->_chart->getLatex($this->_opp->sheets->getView());
        $this->assertTrue(is_string($tex));
        logg($tex);
    }

}