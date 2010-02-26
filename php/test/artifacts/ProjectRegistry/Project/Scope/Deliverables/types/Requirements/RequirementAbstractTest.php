<?php
/**
 * @version $Id: DeliverablesTest.php 716 2010-02-21 14:20:35Z yegor256@yahoo.com $
 */

require_once 'AbstractProjectTest.php';

class RequirementAbstractTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $deliverables = $this->_project->deliverables;
        $deliverables->reload();
        $srs = $deliverables->functional;
        $this->assertTrue(count($srs) > 0, 'empty SRS, why?');

        $srs->rewind();
        $this->_deliverable = $srs->current();
        $this->assertTrue(
            $this->_deliverable instanceof Deliverables_Requirements_Abstract,
            'deliverable not found in the list, why so?'
        );
    }

    public function testDesignMapIsVisible()
    {
        $map = $this->_deliverable->getDesignMap($this->_project);
    }

}