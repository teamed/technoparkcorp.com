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
 * One fin statement
 *
 * @package Artifacts
 */
class theStatement extends ArrayIterator
{
    
    /**
     * Name of supplier (email)
     *
     * @var string
     **/
    protected $_supplier;
    
    /**
     * Construct the class
     *
     * @param string Email of supplier
     * @return void
     */
    public function __construct($supplier) 
    {
        parent::__construct();
        $this->_supplier = $supplier;
        $this->rewind();
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
        
        FaZend_Exception::raise('Statement_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));
    }

    /**
     * Rewind the array and fill it with data
     *
     * @return void
     **/
    public function rewind() 
    {
        foreach (thePayment::retrieveByStatement($this) as $payment)
            $this[] = $payment;
        return parent::rewind();
    }

    /**
     * Get full list of statements
     *
     * @return theStatement[]
     **/
    public static function retrieveAll() 
    {
        $emails = thePayment::retrieve(false)
            ->from('payment', array('supplier'))
            ->group('supplier')
            ->fetchAll();
        
        $list = array();
        foreach ($emails as $email)
            $list[] = new theStatement($email->supplier);
        return $list;
    }
    
    /**
     * Calculate balance
     *
     * @return Model_Cost
     **/
    protected function _getBalance() 
    {
        return thePayment::getStatementBalance($this);
    }
    
    /**
     * Calculate total volume
     *
     * @return Model_Cost
     **/
    protected function _getVolume() 
    {
        return thePayment::getStatementVolume($this);
    }
    
}
