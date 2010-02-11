<?php

require_once 'AbstractProjectTest.php';

class AbstractPluginTest extends AbstractProjectTest
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
        if (!$this->_project->activityList->isLoaded() || !count($this->_project->activityList->activities)) {
            $this->_project->activityList->reload();
        }

        $this->_project->activityList->activities->rewind();
        $this->assertTrue(
            count($this->_project->activityList->activities) > 0,
            "Empty list of activities, why?"
        );

        $this->_activity = $this->_project->activityList->activities->current();
        $this->assertTrue(
            $this->_activity instanceof theActivity, 
            "Activity is not an instance of theActivity, but of " . get_class($this->_activity)
        );
    }

}