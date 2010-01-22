<?php

require_once 'AbstractProjectTest.php';

class ProjectTest extends AbstractProjectTest 
{

    public function testRetrieveAllWorks() 
    {
        $projects = Model_Project::retrieveAll();
    }

    public function testGetStakeholdersByRoleWorks() 
    {
        $project = Mocks_Model_Project::get();
        $list = $project->getStakeholdersByRole('CCB');
    }

}