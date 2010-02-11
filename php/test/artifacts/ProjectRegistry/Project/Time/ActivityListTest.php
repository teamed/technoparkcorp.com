<?php

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/AbstractPluginTest.php';

class ActivityListTest extends AbstractPluginTest
{

    public function testActivityListIsReloadable()
    {
        $list = $this->_project->activityList;
        
        $this->assertTrue(count($list->activities) > 0, "Empty activity list, why?");
        logg(count($list->activities) . ' activities in the list');
    }

}