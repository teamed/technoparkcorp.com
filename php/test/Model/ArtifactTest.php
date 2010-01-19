<?php

require_once 'FaZend/Test/TestCase.php';

class ArtifactTest extends FaZend_Test_TestCase 
{

    public function setUp() {
        parent::setUp();
        // delete all existing objects
        // $this->_dbAdapter->query("DELETE FROM fzObject");
    }

    public function testSimplePosOperationsWork() 
    {
        // Model_Artifact::root()->test = 'test';
    }

}