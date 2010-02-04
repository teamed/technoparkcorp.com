<?php

require_once 'AbstractTest.php';

class theStatementTest extends AbstractTest
{

    public function testVolumeAndBalanceWork() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        
        $volume = $statement->volume;
        $this->assertTrue($volume instanceof FaZend_Bo_Money, 'Volume is not as FaZend_Bo_Money, why?');
        $this->assertTrue(
            $volume->isGreater('100 USD'), 
            "Volume is too small ({$volume}), why?"
        );
        
        $balance = $statement->balance;
        $this->assertTrue($balance instanceof FaZend_Bo_Money, 'Balance is not as FaZend_Bo_Money, why?');
    }
    
    public function testEmptyVolumeAndBalanceCanBeRetrieved()
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        
        // delete all payments
        thePayment::retrieve()->delete();

        $this->assertTrue($statement->volume->isZero(), 'Volume is not empty, why?');
        $this->assertTrue($statement->balance->isZero(), 'Balance is not empty, why?');
    }

    public function testPaymentCollectionWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        $this->assertTrue(count($statement) > 0, 'Statement is empty, why?');
    }

    public function testAsTextWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        
        $this->assertTrue(count($statement) > 0, 'Statement is empty, why?');
        logg('There are ' . count($statement) . ' payments in the statement');
        
        $this->assertTrue(strlen($statement->asText) > 0, 'Statement asText() is empty, why?');
    }

    public function testSendByEmailWorks() 
    {
        require_once 'Mocks/artifacts/Statements/Statement.php';
        $statement = Mocks_theStatement::get();
        $balance = $statement->balance->usd;
        
        // make it positive
        if ($balance <= 0) {
            require_once 'Mocks/artifacts/Statements/Statement/Payment.php';
            Mocks_thePayment::make($statement->supplier, -$balance + 50);
        }
            
        // test its sending
        $statement->sendByEmail();
    }

}