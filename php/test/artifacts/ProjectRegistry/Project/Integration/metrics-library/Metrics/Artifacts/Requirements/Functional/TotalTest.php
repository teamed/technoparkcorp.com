<?php

require_once 'artifacts/ProjectRegistry/Project/Integration/metrics-library/Metrics/AbstractMetricTest.php';

class Metric_Artifacts_Requirements_Functional_TotalTest extends AbstractMetricTest
{
    
    public static function providerTotalReqs()
    {
        // total reqs, levels total, on first level
        return array(
            array(4, 1, 4),
            array(11, 2, 4),
            array(50, 2, 6),
            array(200, 3, 8),
            array(2000, 4, 8),
        );
    }
    
    /**
     * @dataProvider providerTotalReqs
     */
    public function testTotalReqsIsValid($totalReqs, $level, $onFirstLevel)
    {
        $metric = $this->_metrics['artifacts/requirements/functional/total'];
        $this->assertEquals($metric->getTotalLevels($totalReqs), $level);
    }
    
    /**
     * @dataProvider providerTotalReqs
     */
    public function testFirstLevelIsValid($totalReqs, $level, $onFirstLevel)
    {
        $metric = $this->_metrics['artifacts/requirements/functional/total'];
        $this->assertEquals($metric->getTotalOnFirstLevel($totalReqs), $onFirstLevel);
        $this->assertEquals($metric->getTotalOnLevel($totalReqs, 0), $onFirstLevel);
    }
    
    /**
     * @dataProvider providerTotalReqs
     */
    public function testMultiplierIsCalculatable($totalReqs, $level, $onFirstLevel)
    {
        $metric = $this->_metrics['artifacts/requirements/functional/total'];
        $m = $metric->getMultiplier($totalReqs);
    }
    
    /**
     * @dataProvider providerTotalReqs
     */
    public function testTotalIsSplittedProperlyAmongLevels($totalReqs, $level, $onFirstLevel)
    {
        $metric = $this->_metrics['artifacts/requirements/functional/total'];
        $metric->objective = $totalReqs;
        assert($metric->objective == $totalReqs);
        
        $total = 0;
        $log = '';
        foreach ($metric->getLevelCodes() as $level) {
            $m = $this->_metrics['artifacts/requirements/functional/total/level/' . $level];
            $m->reload();
            $total += $m->objective;
            $log .= sprintf('%s: %d, ', $level, $m->objective);
        }
        
        $this->assertEquals(
            $total, 
            $totalReqs, 
            'Invalid calculation, ' . $log
        );
        $metric->objective = null;
    }
    
}