<?php

require_once 'AbstractTest.php';

class theStatementsTest extends AbstractTest
{

    public function testGlobalVolumeAndBalanceWork() 
    {
        $statements = Model_Artifact::root()->statements;
        
        $volume = $statements->volume;
        $this->assertTrue(
            $volume instanceof FaZend_Bo_Money, 
            'Volume is not as FaZend_Bo_Money, but: ' . gettype($volume)
        );
        
        $balance = $statements->balance;
        $this->assertTrue(
            $balance instanceof FaZend_Bo_Money, 
            'Balance is not as FaZend_Bo_Money, but: ' . gettype($balance)
        );
    }

}