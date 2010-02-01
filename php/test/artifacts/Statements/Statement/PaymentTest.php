<?php

require_once 'AbstractTest.php';

class thePaymentTest extends AbstractTest
{

    public function testValidPaymentCanBeCreated() 
    {
        require_once 'Mocks/artifacts/Statements/Statement/Payment.php';
        $payment = Mocks_thePayment::make('test@example.com', '123 USD');
        $this->assertTrue($payment instanceof thePayment);
        $this->assertEquals($payment->supplier, 'test@example.com');
        $this->assertEquals($payment->amount->usd, 123);
    }

}