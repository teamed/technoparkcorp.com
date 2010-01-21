<?php

require_once 'AbstractTest.php';

class ArtifactTest extends AbstractTest 
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