<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'FaZend/Test/TestCase.php';

/**
 * Abstract test case for project-related testing
 *
 * @package test
 */
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

    public function tearDown()
    {
        FaZend_Pos_Abstract::root()->ps()->saveAll();
    }

}