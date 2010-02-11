<?php

require_once 'AbstractProjectTest.php';

class ActivityTest extends AbstractProjectTest
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
        $this->_project->activityList->activities->rewind();
        $this->_activity = $this->_project->activityList->activities->current();
    }

    public function testActivityIsAccessible()
    {
        $this->assertTrue(
            count($this->_project->activityList->activities) > 0, 
            "Empty activity list, why?"
        );
    
        $this->assertTrue(
            $this->_activity instanceof theActivity, 
            "Activity is not an instance of theActivity, but of " . get_class($this->_activity)
        );
        
        $this->assertTrue(is_string($this->_activity->name), "NAME is not a string, why?");
        $this->assertTrue(is_string($this->_activity->id), "ID is not a string, why?");
        $this->assertTrue(is_string($this->_activity->description), "DESCRIPTION is not a string, why?");
        $this->assertTrue($this->_activity->project instanceof theProject, "Project is not a theProject, why?");
        $this->assertTrue(is_string($this->_activity->sow), "SOW is not a string, why?");
        
        $this->assertTrue(is_string($this->_activity->doc), "DOC is not a string, why?");
        $this->assertTrue($this->_activity->start instanceof Zend_Date, "START is not a date, why?");
        $this->assertTrue($this->_activity->finish instanceof Zend_Date, "FINISH is not a date, why?");
        $this->assertTrue(is_integer($this->_activity->duration), "DURATION is not an integer, why?");

        $this->assertTrue($this->_activity->criteria instanceof theActivityCriteria, "CRITERIA is not an object, why?");
        $this->assertTrue($this->_activity->predecessors instanceof theActivityPredecessors, "PREDECESSORS is not an object, why?");
        
        logg(
            "activity name: {$this->_activity->name}, " .
            "id: {$this->_activity->id}, " . 
            "description: {$this->_activity->description}, " . 
            "project: {$this->_activity->project}, " . 
            "sow: {$this->_activity->sow}, " . 
            "doc: {$this->_activity->doc}, " . 
            "start: {$this->_activity->start}, " . 
            "finish: {$this->_activity->finish}, " . 
            "duration: {$this->_activity->duration}, " . 
            "criteria: {$this->_activity->criteria}, " . 
            "predecessors: {$this->_activity->predecessors}, "
            );
    }

}