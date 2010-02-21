<?php
/**
 * @version $Id$
 */

require_once 'artifacts/ProjectRegistry/Project/Integration/metrics-library/Metrics/AbstractMetricTest.php';

class Metric_Artifacts_Requirements_Functional_TotalTest extends AbstractMetricTest
{
    
    public function testTotalIsCalculated()
    {
        $metric = $this->_metrics['artifacts/requirements/functional/total'];
        $this->assertTrue($metric->value > 0);
    }
    
}

