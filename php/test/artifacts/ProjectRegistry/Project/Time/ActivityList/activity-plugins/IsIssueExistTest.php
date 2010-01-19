<?php

require_once 'AbstractProjectTest.php';

class IsIssueExistTest extends AbstractProjectTest
{

    public function testActivityFlagIsReadable()
    {
        $this->_project->activityList->activities->rewind();
        $activity = $this->_project->activityList->activities->current();
        $this->assertTrue($activity instanceof theActivity, "Activity is not an instance of theActivity, but of " . get_class($activity));
    
        $flag = $activity->isIssueExist();
        $this->assertTrue(is_bool($flag), "Flag is not boolean, why?");
    }

}