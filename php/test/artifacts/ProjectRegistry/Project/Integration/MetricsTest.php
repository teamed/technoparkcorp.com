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
            logg("Metric [$name]: {$metric->value}, {$metric->default}, {$metric->target}, {$metric->delta}");
    }

}