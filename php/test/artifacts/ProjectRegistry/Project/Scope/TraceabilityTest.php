<?php
/**
 * @version $Id$
 */

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
        $this->assertTrue(($coverage <= 1) && ($coverage > 0), 'Strange value of coverage: ' . $coverage);
        logg('coverage: %0.2f%%', $coverage * 100);
    }

    public function testGetCoverageSourcesWorks()
    {
        $sources = $this->_traceability->getCoverageSources('issues', 'functional');
        $this->assertFalse(empty($sources), 'Empty list of sources, why?');
    }

}