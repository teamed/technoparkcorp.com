<?php

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/AbstractPluginTest.php';

class ActivityTest extends AbstractPluginTest
{

    public function testActivityIsAccessible()
    {
        $a = $this->_activity;
        
        $this->assertTrue(is_string($a->name), "NAME is not a string, why?");
        $this->assertTrue(is_string($a->id), "ID is not a string, why?");
        $this->assertTrue(is_string($a->description), "DESCRIPTION is not a string, why?");
        $this->assertTrue($a->project instanceof theProject, "Project is not a theProject, why?");
        $this->assertTrue(is_string($a->sow), "SOW is not a string, why?");
        
        $this->assertTrue(is_string($a->doc), "DOC is not a string, why?");
        $this->assertTrue($a->start instanceof Zend_Date, "START is not a date, why?");
        $this->assertTrue($a->finish instanceof Zend_Date, "FINISH is not a date, why?");
        $this->assertTrue(is_integer($a->duration), "DURATION is not an integer, why?");

        $this->assertTrue($a->criteria instanceof theActivityCriteria, "CRITERIA is not an object, why?");
        $this->assertTrue($a->predecessors instanceof theActivityPredecessors, "PREDECESSORS is not an object, why?");
        
        logg(
            "activity name: {$a->name}, " .
            "id: {$a->id}, " . 
            "description: {$a->description}, " . 
            "project: {$a->project}, " . 
            "sow: {$a->sow}, " . 
            "doc: {$a->doc}, " . 
            "start: {$a->start}, " . 
            "finish: {$a->finish}, " . 
            "duration: {$a->duration}, " . 
            "criteria: {$a->criteria}, " . 
            "predecessors: {$a->predecessors}, "
        );
    }

}