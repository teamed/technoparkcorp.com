<?php

require_once 'FaZend/Test/TestCase.php';

class theStatementsTest extends FaZend_Test_TestCase
{

    public function testGlobalVolumeAndBalanceWork() 
    {
        $statements = Model_Artifact::root()->statements;
        
        $volume = $statements->volume;
        $this->assertTrue($volume instanceof FaZend_Bo_Money, 
            'Volume is not as FaZend_Bo_Money, why?');
        
        $balance = $statements->balance;
        $this->assertTrue($balance instanceof FaZend_Bo_Money, 
            'Balance is not as FaZend_Bo_Money, why?');
    }

}