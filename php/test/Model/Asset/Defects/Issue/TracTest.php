<?php

require_once 'FaZend/Test/TestCase.php';

class Model_Asset_Defects_Issue_TracTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = $this->_project->fzProject();
        $asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $this->_issue = $asset->findById(1);
    }

    public function testBasicMethodsWork() 
    {
        $this->assertTrue($this->_issue instanceof Model_Asset_Defects_Issue_Abstract);
    }

}
