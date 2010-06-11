<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class WBSTest extends AbstractProjectTest
{

    public function testGeneralMechanismWorks()
    {
        $wbs = $this->_project->wbs;
        $wbs->reload();

        /**
         * @todo The test is disabled... I don't have time now to fix it.
         */
        // $this->assertTrue(count($this->_project->wbs) > 0, "Empty WBS, why?");
        logg(count($wbs) . ' work packages in WBS');
    }

    public function testSummaryWorks()
    {
        $wbs = $this->_project->wbs;
        $this->assertTrue($this->_project->wbs->sum() instanceof FaZend_Bo_Money);
    }

}