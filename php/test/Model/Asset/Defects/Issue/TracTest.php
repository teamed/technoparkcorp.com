<?php

require_once 'AbstractProjectTest.php';

class Model_Asset_Defects_Issue_TracTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = $this->_project->fzProject();
        $asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $this->_issue = $asset->findById(1);
    }

    public function testTicketObjectIsAlwaysTheSame() 
    {
        $asset = $this->_project->fzProject()->getAsset(Model_Project::ASSET_DEFECTS);
        // just to make sure that the list of tickets was retrieved again
        $asset->retrieveBy();
        $issue = $asset->findById(1);
        $this->assertTrue($this->_issue === $issue, 'Objects are different');
    }

    public function testBasicMethodsWork() 
    {
        $this->assertTrue($this->_issue instanceof Model_Asset_Defects_Issue_Abstract);
    }

}
