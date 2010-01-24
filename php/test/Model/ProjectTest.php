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

    public function testAssetAlwaysTheSame() 
    {
        $project = Mocks_Model_Project::get();
        $asset1 = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $asset2 = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $this->assertTrue($asset1 === $asset2);
    }

}