<?php
/**
 * @version $Id$
 */

require_once 'artifacts/ProjectRegistry/Project/Integration/metrics-library/Metrics/AbstractMetricTest.php';

class Metric_Artifacts_Product_Functionality_ImplementedTest extends AbstractMetricTest
{
    
    public function testImplementedRequirementsNumberIsInRange()
    {
        $metric = $this->_metrics['artifacts/product/functionality/implemented'];
        $this->assertTrue($metric->value <= 1 && $metric->value >= 0);
    }
    
}