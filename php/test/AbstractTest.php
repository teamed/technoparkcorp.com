<?php

require_once 'FaZend/Test/TestCase.php';

abstract class AbstractTest extends FaZend_Test_TestCase
{

    public function tearDown() 
    {
        parent::tearDown();

        Model_Artifact::root()->ps()->save();
        Model_Article::lucene()->commit();
    }

}