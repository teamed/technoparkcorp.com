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
     * @param string Reason of the payment
     * @param string Details of the payment
     * @return FaZend_Db_Table_ActiveRow_payment
     **/
    public static function create($supplier, $rate, $original, $context, $reason, $details) 
    {
        validate()
            ->emailAddress($supplier, array());
        
        $payment = new thePayment();
        $payment->supplier = $supplier;
        $payment->context = $context;
        $payment->reason = $reason;
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
     * Get total volume
     *
     * @return Model_Cost
     **/
    public static function getVolume() 
    {
        return Model_Cost::factory(intval(thePayment::retrieve()
            ->columns(array('volume'=>new Zend_Db_Expr('SUM(IF(amount>0,amount,0))/100')))
            ->fetchRow()
            ->volume) . ' USD');
    }
    
    /**
     * Get total balance
     *
     * @return Model_Cost
     **/
    public static function getBalance() 
    {
        return Model_Cost::factory(intval(thePayment::retrieve()
            ->columns(array('balance'=>new Zend_Db_Expr('SUM(amount)/100')))
            ->fetchRow()
            ->balance) . ' USD');
    }
    
    /**
     * Get total volume of the given statement
     *
     * @param theStatement The statement to analyze
     * @return Model_Cost
     **/
    public static function getStatementVolume(theStatement $statement) 
    {
        return Model_Cost::factory(intval(thePayment::retrieve()
            ->columns(array('volume'=>new Zend_Db_Expr('SUM(IF(amount>0,amount,0))/100')))
            ->where('supplier = ?', $statement->supplier)
            ->group('supplier')
            ->fetchRow()
            ->volume) . ' USD');
    }
    
    /**
     * Get balance of the given statement
     *
     * @param theStatement The statement to analyze
     * @return Model_Cost
     **/
    public static function getStatementBalance(theStatement $statement) 
    {
        return Model_Cost::factory(intval(thePayment::retrieve()
            ->columns(array('balance'=>new Zend_Db_Expr('SUM(amount)/100')))
            ->where('supplier = ?', $statement->supplier)
            ->group('supplier')
            ->fetchRow()
            ->balance) . ' USD');
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        return parent::__get($name);
    }

    /**
     * Get amount in USD
     *
     * @return Model_Cost
     **/
    protected function _getUsd() {
        return Model_Cost::factory($this->amount / 100);
    }
           
}
