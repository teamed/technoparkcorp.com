<?php

require_once 'AbstractProjectTest.php';

class MetricsTest extends AbstractProjectTest
{
    
    public function setUp()
    {
        parent::setUp();
        
        // explicitly reload them
        $this->_project->metrics->reload();
    }
    
    public function testMetricsAreAccessibleInStorage() 
    {
        $defects = $this->_project->metrics['artifacts/defects/total']->value;
        logg($defects . ' defects found');
    }
    
    public function testFullRetrievalOfMetricsWork()
    {
        $list = $this->_project->metrics;
        logg(count($list) . ' metrics found');
        
        foreach ($list as $name=>$metric)
            logg("Metric [$name]: {$metric->value}, {$metric->default}, {$metric->objective}, {$metric->delta}");
    }
    
    public static function providerMetricNames()
    {
        $project = Mocks_theProject::get();
        return array(
            array('artifacts/requirements/functional/total/level/third'),
            array('artifacts/defects/total/byOwner/' . $project->staffAssignments->PM->random()),
            array('artifacts/defects/total/byReporter/' . $project->staffAssignments->PM->random()),
            array('aspects/coverage/classes/issues'),
            array('aspects/coverage/functional/classes'),
        );
    }
    
    /**
     * @dataProvider providerMetricNames
     */
    public function testReloadIndividualMetric($name)
    {
        $metric = $this->_project->metrics[$name];
        $metric->reload();
        $value = $metric->value;
        $this->assertTrue(!is_null($value));
        logg(
            '[%s] works: %s',
            $name, 
            $value
        );
    }

}