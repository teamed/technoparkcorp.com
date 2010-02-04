<?php

require_once 'AbstractTest.php';

class theStatementsTest extends AbstractTest
{

    public function testGlobalVolumeAndBalanceWork() 
    {
        $statements = Model_Artifact::root()->statements;

        // to make sure some statement exists
        require_once 'Mocks/artifacts/Statements/Statement.php';
        Mocks_theStatement::get();
        
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

    public function testEmptyGlobalVolumeAndBalanceCanBeRetrieved() 
    {
        $statements = Model_Artifact::root()->statements;
        // to make sure some statement exists
        require_once 'Mocks/artifacts/Statements/Statement.php';
        Mocks_theStatement::get();
        
        $this->assertFalse($statements->volume->isZero(), 'Volume is empty, why?');
        $this->assertFalse($statements->balance->isZero(), 'Balance is empty, why?');

        // delete all payments
        thePayment::retrieve()->delete();
        
        $this->assertTrue($statements->volume->isZero(), 'Volume is not empty, why?');
        $this->assertTrue($statements->balance->isZero(), 'Balance is not empty, why?');
    }

}