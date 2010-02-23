<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
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
    public static function create(
        $supplier, 
        FaZend_Bo_Money $rate, 
        FaZend_Bo_Money $original, 
        $context, 
        $reason, 
        $details
    ) 
    {
        validate()
            ->emailAddress($supplier, array(), "Invalid format of supplier's email: '{$email}'");

        $payment = new thePayment();
        $payment->supplier = strval($supplier);
        $payment->context = $context;
        $payment->reason = $reason;
        $payment->details = $details;
        $payment->rate = $rate;
        $payment->original = $original;
        $payment->amount = $original->cents;
        
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
        return self::retrieve()
            ->where('supplier = ?', strval($statement->supplier))
            ->order('created')
            ->setRowClass('thePayment')
            ->fetchAll();
    }
    
    /**
     * Get summary of payments made in the specified interval, grouped by supplier
     *
     * @param Zend_Date Start
     * @param Zend_Date End
     * @return array[]
     */
    public static function retrieveSummaryByInterval(Zend_Date $start, Zend_Date $end) 
    {
        return self::retrieve()
            ->columns(
                array(
                    'paid' => new Zend_Db_Expr('SUM(IF(context = "" AND amount < 0, -amount, 0))'),
                    'earned' => new Zend_Db_Expr('SUM(IF(context <> "", amount, 0))'),
                )
            )
            ->where('created BETWEEN :start AND :end')
            ->group('supplier')
            ->fetchAll(
                array(
                    'start' => $start->getIso(),
                    'end' => $end->getIso(),
                )
            );
    }
    
    /**
     * Get total volume
     *
     * @return FaZend_Bo_Money
     **/
    public static function getVolume() 
    {
        return self::retrieve()
            ->columns(array('volume'=>new Zend_Db_Expr('SUM(IF(amount>0,amount,0))')))
            ->setRowClass('thePayment')
            ->fetchRow()
            ->setIgnoreNull()
            ->volume;
    }
    
    /**
     * Get total balance
     *
     * @return FaZend_Bo_Money
     **/
    public static function getBalance() 
    {
        return self::retrieve()
            ->columns(array('balance'=>new Zend_Db_Expr('SUM(amount)')))
            ->setRowClass('thePayment')
            ->fetchRow()
            ->setIgnoreNull()
            ->balance;
    }
    
    /**
     * Get total volume of the given statement
     *
     * @param theStatement The statement to analyze
     * @return FaZend_Bo_Money
     **/
    public static function getStatementVolume(theStatement $statement) 
    {
        return self::retrieve()
            ->columns(array('volume'=>new Zend_Db_Expr('SUM(IF(amount>0,amount,0))')))
            ->where('supplier = ?', strval($statement->supplier))
            ->setRowClass('thePayment')
            ->fetchRow()
            ->setIgnoreNull()
            ->volume;
    }
    
    /**
     * Get balance of the given statement
     *
     * @param theStatement The statement to analyze
     * @return FaZend_Bo_Money
     **/
    public static function getStatementBalance(theStatement $statement) 
    {
        return self::retrieve()
            ->columns(array('balance'=>new Zend_Db_Expr('SUM(amount)')))
            ->where('supplier = ?', strval($statement->supplier))
            ->setRowClass('thePayment')
            ->fetchRow()
            ->setIgnoreNull()
            ->balance;
    }
    
    /**
     * How much we paid already to this supplier in the given project
     *
     * @param theStakeholder
     * @param theProject
     * @return FaZend_Bo_Money
     **/
    public static function getPaidInProjectToStakeholder(theStakeholder $stakeholder, theProject $project) 
    {
        return self::retrieve()
            ->columns(array('volume'=>new Zend_Db_Expr('SUM(amount)')))
            ->where('supplier = ?', $stakeholder->email)
            ->where('context = ?', $project->name)
            ->group('supplier')
            ->setRowClass('thePayment')
            ->fetchRow()
            ->setIgnoreNull()
            ->volume;
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
        if (method_exists($this, $method)) {
            return $this->$method();
        }
            
        $var = '_' . $name;
        if (property_exists($this, $var)) {
            return $this->$var;
        }
        
        return parent::__get($name);
    }

}
