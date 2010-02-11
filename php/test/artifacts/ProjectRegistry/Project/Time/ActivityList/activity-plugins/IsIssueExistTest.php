<?php

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/AbstractPluginTest.php';

class IsIssueExistTest extends AbstractPluginTest
{

    public function testActivityFlagIsReadable()
    {
        $flag = $this->_activity->isIssueExist();
        $this->assertTrue(is_bool($flag), "Flag is not boolean, why?");
    }

}