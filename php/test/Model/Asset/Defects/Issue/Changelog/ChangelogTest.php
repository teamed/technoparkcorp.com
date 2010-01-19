<?php

require_once 'FaZend/Test/TestCase.php';

class Model_Asset_Defects_Issue_Changelog_ChangelogTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = $this->_project->fzProject();
        $asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $issue = $asset->findById(1);
        $this->_changelog = $issue->changelog;
    }

    public function testBasicMethodsWork() 
    {
        $this->assertTrue($this->_changelog->get('owner') instanceof Model_Asset_Defects_Issue_Changelog_Field_Abstract);
    }

}
