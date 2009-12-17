<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */


/**
 * One payment
 *
 * @package Artifacts
 */
class thePayment extends FaZend_Db_Table_ActiveRow_payment
{

    /**
     * Create new payment
     *
     * @param string Email of the supplier
     * @param string Active rate at the moment of payment, like '12 USD'
     * @param string Original amount of payment, like '125 EUR'
     * @param string Context, for example name of project
     * @param string Details of the payment
     * @return FaZend_Db_Table_ActiveRow_payment
     **/
    public static function create($supplier, $rate, $original, $context, $details) 
    {
        validate()
            ->emailAddress($supplier, array());
        
        $payment = new thePayment();
        $payment->supplier = $supplier;
        $payment->context = $context;
        $payment->details = $details;
        $payment->rate = $rate;
        $payment->original = $original;
        
        $payment->amount = (integer)Model_Cost::factory($original)->cents;
        
        $payment->save();
        return $payment;
    }
        
    /**
     * Get full list of payments in the project
     *
     * @param theStatement The statement to get payments from
     * @return thePayment[]
     **/
    public static function retrieveByStatement(theStatement $statement) 
    {
        return thePayment::retrieve()
            ->where('supplier = ?', $statement->supplier)
            ->order('created')
            ->setRowClass('thePayment')
            ->fetchAll();
    }
    
    /**
     * Get amount in USD
     *
     * @return Model_Cost
     **/
    public function getCost() {
        return Model_Cost::factory($this->amount / 100);
    }
           
}
