<?php

require_once 'AbstractProjectTest.php';

class StaffAssignmentsTest extends AbstractProjectTest 
{

    public function testGeneralMechanismWorks() 
    {
        $sa = $this->_project->staffAssignments;
        $this->assertTrue($sa instanceof theStaffAssignments, 
            "staffAssignments is not defined, why?\n" . $this->_project->ps()->dump(false));
        
        $CCB = $sa->CCB;
        logg('Change Control Board in test project: ' . $CCB);
        
        $this->assertTrue($sa->hasRole('PM'),
            "Test project doesn't have PM role? why?");
    }

}