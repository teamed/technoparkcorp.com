<?php

require_once 'FaZend/Test/TestCase.php';

abstract class AbstractTest extends FaZend_Test_TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        
        // save all changes made to POS, if they were made
        FaZend_Pos_Properties::saveAll();
    }

}