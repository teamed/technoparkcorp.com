<?php

require_once 'AbstractProjectTest.php';

class IsIssueExistTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        if (!$this->_project->metrics->isLoaded())
            $this->_project->metrics->reload();
        if (!$this->_project->wbs->isLoaded())
            $this->_project->wbs->reload();
        if (!$this->_project->activityList->isLoaded())
            $this->_project->activityList->reload();
    }

    public function testActivityFlagIsReadable()
    {
        $this->_project->activityList->activities->rewind();
        $activity = $this->_project->activityList->activities->current();
        $this->assertTrue($activity instanceof theActivity, 
            "Activity is not an instance of theActivity, but of " . get_class($activity));
    
        $flag = $activity->isIssueExist();
        $this->assertTrue(is_bool($flag), "Flag is not boolean, why?");
    }

}