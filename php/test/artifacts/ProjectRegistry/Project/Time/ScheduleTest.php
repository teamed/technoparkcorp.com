<?php

require_once 'AbstractProjectTest.php';

class ScheduleTest extends AbstractProjectTest
{

    public function testScheduleCanBeReloaded()
    {
        $schedule = $this->_project->schedule;
        $schedule->reload();
        
        $this->assertTrue(
            $schedule->finish instanceof Zend_Date,
            'Strange result from theSchedule::finish'
            );
    }

}