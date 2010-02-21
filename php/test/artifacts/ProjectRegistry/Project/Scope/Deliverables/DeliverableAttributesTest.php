<?php
/**
 * @version $Id: DeliverablesTest.php 716 2010-02-21 14:20:35Z yegor256@yahoo.com $
 */

require_once 'AbstractProjectTest.php';

class DeliverableAttributesTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $deliverables = $this->_project->deliverables;
        $deliverables->reload();
        $design = $deliverables->design;
        $this->assertTrue(count($design) > 0, 'empty design, why?');

        $design->rewind();
        $this->_deliverable = $design->current();
        $this->assertTrue(
            $this->_deliverable instanceof Deliverables_Abstract,
            'deliverable not found in the list, why so?'
        );
    }

    public function testListOfAttributesIsAccessible()
    {
        $this->assertTrue(
            $this->_deliverable->attributes instanceof theDeliverableAttributes,
            'attributes are of type: ' . gettype($this->_deliverable->attributes)
        );
    }

    public function testNonExistingAttributesReturnFalse()
    {
        $this->assertFalse($this->_deliverable->attributes['missed for sure']->isTrue());
    }

}