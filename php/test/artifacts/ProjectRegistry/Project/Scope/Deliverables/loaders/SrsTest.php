<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class Deliverables_loaders_SrsTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $this->_loader = DeliverablesLoaders_Abstract::factory(
            'srs',
            $this->_project->deliverables
        );
    }

    public function testLoadingWorks()
    {
        $this->_loader->load();
    }

}