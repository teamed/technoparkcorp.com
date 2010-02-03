<?php

require_once 'AbstractProjectTest.php';

class TraceabilityTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        if (!$this->_project->deliverables->isLoaded()) {
            $this->_project->deliverables->reload();
        }
        $this->_traceability = $this->_project->traceability;
        
    }

    public function testCoverageCanBeCalculated()
    {
        $coverage = $this->_traceability->getCoverage('issues', 'functional');
        $this->assertFalse(empty($coverage), 'Empty coverage, why?');
        logg('coverage: %0.2f%%', $coverage * 100);
    }

}