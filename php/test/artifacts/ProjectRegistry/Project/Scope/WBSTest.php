<?php

require_once 'AbstractProjectTest.php';

class WBSTest extends AbstractProjectTest
{

    public function testGeneralMechanismWorks()
    {
        $wbs = $this->_project->wbs;
        $wbs->reload();
        
        $this->assertTrue(count($this->_project->wbs) > 0, "Empty WBS, why?");
        logg(count($wbs) . ' work packages in WBS');
    }

}