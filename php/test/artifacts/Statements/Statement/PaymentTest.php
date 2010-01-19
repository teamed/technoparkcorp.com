<?php

require_once 'FaZend/Test/TestCase.php';

class thePaymentTest extends FaZend_Test_TestCase
{

    public function testValidPaymentCanBeCreated() 
    {
        require_once 'Mocks/artifacts/Statements/Statement/Payment.php';
        $payment = Mocks_thePayment::make('test@example.com', '123 USD');
        $this->assertTrue($payment instanceof thePayment);
        $this->assertEquals($payment->supplier, 'test@example.com');
        $this->assertEquals($payment->usd->usd, 123);
    }

}