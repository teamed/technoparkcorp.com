<?php
/**
 * @version $Id$
 */

class Mocks_thePayment 
{

    public static function make($supplier = 'test@example.com', $original = '100 EUR') 
    {
        return thePayment::create(
            $supplier, // supplier
            new FaZend_Bo_Money('12 USD'), // rate per hour
            new FaZend_Bo_Money($original), // original
            'test', // context
            'test.' . time() . '.' . (microtime(true) * 1000), // reason, should be new every time
            'created by ' . get_class()
            );
    }
    
}
