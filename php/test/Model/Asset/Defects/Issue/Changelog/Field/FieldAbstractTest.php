<?php

require_once 'AbstractProjectTest.php';

class Model_Asset_Defects_Issue_Changelog_Changelog_Field_FieldAbstractTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = $this->_project->fzProject();
        $asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $issue = $asset->findById(1);
        $this->_field = $issue->changelog->get('comment');
    }

    public function testFieldObjectIsAlwaysTheSame() 
    {
        $field = $this->_project
            ->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS)
            ->findById(1)
            ->changelog
            ->get('comment');
        $this->assertTrue($this->_field === $field, 'objects are different, why?');
    }

    public function testBasicMethodsWork() 
    {
        $this->assertTrue(is_bool($this->_field->wasChanged()));
        $this->assertTrue(is_string($this->_field->getValue()));
        $this->assertTrue($this->_field->getLastDate() instanceof Zend_Date);
    }

}
