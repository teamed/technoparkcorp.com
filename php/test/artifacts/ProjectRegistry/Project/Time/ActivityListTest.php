<?php

require_once 'AbstractProjectTest.php';

class ActivityListTest extends AbstractProjectTest
{

    public function testActivityListIsReloadable()
    {
        $this->assertTrue(count($this->_project->wbs) > 0, "Empty WBS, why?");
        
        $list = $this->_project->activityList;
        $list->reload();
        
        $this->assertTrue(count($list->activities) > 0, "Empty activity list, why?");
        
        logg(count($list->activities) . ' activities in the list');
    }

}