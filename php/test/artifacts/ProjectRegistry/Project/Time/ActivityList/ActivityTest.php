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
    }

    public function testActivityIsAccessible()
    {
        $this->assertTrue(count($this->_project->activityList->activities) > 0, "Empty activity list, why?");
    
        $this->_project->activityList->activities->rewind();
        $activity = $this->_project->activityList->activities->current();
        $this->assertTrue($activity instanceof theActivity, "Activity is not an instance of theActivity, but of " . get_class($activity));
        
        $this->assertTrue(is_string($activity->name), "NAME is not a string, why?");
        $this->assertTrue(is_string($activity->id), "ID is not a string, why?");
        $this->assertTrue(is_string($activity->description), "DESCRIPTION is not a string, why?");
        $this->assertTrue($activity->project instanceof theProject, "Project is not a theProject, why?");
        $this->assertTrue(is_string($activity->sow), "SOW is not a string, why?");
        
        $this->assertTrue(is_string($activity->doc), "DOC is not a string, why?");
        $this->assertTrue($activity->start instanceof Zend_Date, "START is not a date, why?");
        $this->assertTrue($activity->finish instanceof Zend_Date, "FINISH is not a date, why?");
        $this->assertTrue(is_integer($activity->duration), "DURATION is not an integer, why?");

        $this->assertTrue($activity->criteria instanceof theActivityCriteria, "CRITERIA is not an object, why?");
        $this->assertTrue($activity->predecessors instanceof theActivityPredecessors, "PREDECESSORS is not an object, why?");
        
        logg(
            "activity name: {$activity->name}, " .
            "id: {$activity->id}, " . 
            "description: {$activity->description}, " . 
            "project: {$activity->project}, " . 
            "sow: {$activity->sow}, " . 
            "doc: {$activity->doc}, " . 
            "start: {$activity->start}, " . 
            "finish: {$activity->finish}, " . 
            "duration: {$activity->duration}, " . 
            "criteria: {$activity->criteria}, " . 
            "predecessors: {$activity->predecessors}, "
            );
    }

}