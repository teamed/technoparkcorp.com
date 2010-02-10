<?php

require_once 'AbstractProjectTest.php';

class ActivityListTest extends AbstractProjectTest
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
    }

    public function testActivityListIsReloadable()
    {
        $list = $this->_project->activityList;
        
        $this->assertTrue(count($list->activities) > 0, "Empty activity list, why?");
        logg(count($list->activities) . ' activities in the list');
    }

}