<?php

class Mocks_theStatement 
{

    public static function get($supplier = 'test@example.com') 
    {
        require_once 'Mocks/artifacts/Statements/Statement/Payment.php';

        // kill payments, if they are too many of them
        if (count(thePayment::retrieve()->fetchAll()) > 50) {
            thePayment::retrieve()->table()->getAdapter()->query('DELETE FROM payment');
        }
            
        for ($i = 0; $i < 5; $i++)
            Mocks_thePayment::make($supplier, (rand(1,9) > 5 ? '' : '-') . rand(100, 200) . ' EUR');
            
        return Model_Artifact::root()->statements[$supplier];
    }
    
}
