<?php

require_once 'FaZend/Test/TestCase.php';

abstract class AbstractProjectTest extends FaZend_Test_TestCase 
{

    public function setUp() 
    {
        parent::setUp();
        
        // get the test project out of registry
        $this->_project = Model_Artifact::root()->projectRegistry[Mocks_Model_Project::NAME];
        
        // make sure it is the thing we're looking for
        $this->assertTrue($this->_project instanceof Mocks_theProject, 
            'Invalid project type (' . get_class($this->_project) . '), why?');
        $this->assertTrue($this->_project->name == Mocks_Model_Project::NAME, 
            'Project name is not the testing one (' . Mocks_Model_Project::NAME . '), why?');
        $this->assertTrue($this->_project->isLoaded(), 
            'The project is not loaded, why?');
    }

}