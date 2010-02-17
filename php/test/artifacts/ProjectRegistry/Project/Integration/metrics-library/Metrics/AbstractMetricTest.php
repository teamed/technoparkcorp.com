<?php

require_once 'AbstractProjectTest.php';

abstract class AbstractMetricTest extends AbstractProjectTest
{
    
    public function setUp()
    {
        parent::setUp();
        // explicitly reload them
        if (!$this->_project->metrics->isLoaded()) {
            $this->_project->metrics->reload();
        }
        $this->_metrics = $this->_project->metrics;
    }
    
}