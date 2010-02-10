<?php

require_once 'AbstractProjectTest.php';

class IsClosedTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        if (!$this->_project->metrics->isLoaded()) {
            $this->_project->metrics->reload();
        }
        if (!$this->_project->wbs->isLoaded()) {
            $this->_project->wbs->reload();
        }
        if (!$this->_project->activityList->isLoaded()) {
            $this->_project->activityList->reload();
        }
        $this->_project->activityList->activities->rewind();
        $this->_activity = $this->_project->activityList->activities->current();
    }

    public function testActivityFlagIsReadable()
    {
        $this->assertTrue(
            $this->_activity instanceof theActivity, 
            "Activity is not an instance of theActivity, but of " . get_class($this->_activity)
        );
    
        $flag = $this->_activity->isClosed();
        $this->assertTrue(is_bool($flag), "Flag is not boolean, why?");
    }

}