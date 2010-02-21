<?php
/**
 * @version $Id$
 */

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

    public function testDifferentProjectsHaveDifferentAssets() 
    {
        $project = Mocks_Model_Project::get();
        $project2 = Mocks_Model_Project::get(2);
        $this->assertFalse($project1 === $project2, 'Projects are the same, why??');

        $asset1 = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $asset2 = $project2->getAsset(Model_Project::ASSET_DEFECTS);
        $this->assertFalse($asset1 === $asset2, 'Assets are the same, why??');
    }

}