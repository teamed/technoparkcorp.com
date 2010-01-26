<?php

require_once 'AbstractProjectTest.php';

class DeliverablesTest extends AbstractProjectTest
{

    public function testReloadingOfDeliverablesWorks()
    {
        $deliverables = $this->_project->deliverables;
        $deliverables->reload();
    }

}