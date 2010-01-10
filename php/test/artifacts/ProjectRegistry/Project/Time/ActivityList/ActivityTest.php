<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'AbstractProjectTest.php';

class ActivityTest extends AbstractProjectTest
{

    public function testActivityIsAccessible()
    {
        if (!$this->_project->activityList->isLoaded())
            $this->_project->activityList->reload();

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