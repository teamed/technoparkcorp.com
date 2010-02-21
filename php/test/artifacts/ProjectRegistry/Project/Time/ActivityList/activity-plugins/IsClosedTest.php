<?php
/**
 * @version $Id$
 */

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/AbstractPluginTest.php';

class IsClosedTest extends AbstractPluginTest
{

    public function testActivityFlagIsReadable()
    {
        $flag = $this->_activity->isClosed();
        $this->assertTrue(is_bool($flag), "Flag is not boolean, why?");
    }

}