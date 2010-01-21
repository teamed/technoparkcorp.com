<?php

require_once 'FaZend/Test/TestCase.php';

abstract class AbstractTest extends FaZend_Test_TestCase
{

    public function tearDown() 
    {
        parent::tearDown();

        FaZend_Pos_Properties::cleanPosMemory(true);
        Model_Article::lucene()->commit();
    }

}