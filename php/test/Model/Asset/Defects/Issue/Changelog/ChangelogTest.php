<?php

require_once 'AbstractProjectTest.php';

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

    public function testChangelogIsAlwaysUniqueObject()
    {
        $changelog = $this->_project->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS)->findById(1)->changelog;
        $this->assertTrue($changelog === $this->_changelog);
    }

    public function testBasicMethodsWork() 
    {
        $this->assertTrue($this->_changelog->get('owner') instanceof Model_Asset_Defects_Issue_Changelog_Field_Abstract);
    }

    public function testAllFieldsHaveValues() 
    {
        $log = array();
        foreach (array('comment', 'status', 'owner', 'summary', 'description') as $name) {
            $this->assertFalse(is_null($this->_changelog->get($name)->getValue()));
            $log[] = $name . '=' . $this->_changelog->get($name)->getValue();
        }
        logg(implode(', ', $log));
    }

}
