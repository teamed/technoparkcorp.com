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
 * One fin statement, sent to suppliers
 *
 * @package Artifacts
 */
class theStatement extends Zend_Db_Table_Row implements ArrayAccess, Iterator, Countable
{
    
    /**
     * Rowset with payments
     *
     * @var thePayment[]
     **/
    protected $_rowset;
    
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

    /**
     * Find statement by supplier
     *
     * @param string Email of the supplier
     * @return theStatement
     **/
    public static function findBySupplier($email) 
    {
        return thePayment::retrieve(false)
            ->from('payment', array('supplier'))
            ->group('supplier')
            ->where('supplier = ?', $email)
            ->setRowClass('theStatement')
            ->fetchRow();
    }

    /**
     * Get full list of statements
     *
     * @return theStatement[]
     */
    public static function retrieveAll() 
    {
        return thePayment::retrieve(false)
            ->from('payment', array('supplier'))
            ->group('supplier')
            ->setRowClass('theStatement')
            ->fetchAll();
    }
    
    /**
     * Send this statement by email to the supplier
     *
     * @return void
     */
    public function sendByEmail() 
    {
        validate()
            ->true($this->balance->usd > 0, "Can't email empty of negative statement ({$this->balance})");
            
        FaZend_Email::create()
            ->set('body', $this->asText)
            ->set('subject', 'TechnoPark Corp. is ready to pay ' . $this->balance)
            ->set('toEmail', $this->supplier)
            ->set('toName', $this->supplier)
            ->set('fromEmail', 'finance@tpc2.com')
            ->set('fromName', 'TechnoPark Corp.')
            ->set('cc', array('finance@tpc2.com'=>'TechnoPark Corp.'))
            ->send();
        logg("Statement sent to " . $this->supplier . ' for ' . $this->balance);
    }
    
    /**
     * Get it as text
     *
     * @return string
     **/
    protected function _getAsText() 
    {
        $text = 
            "Dear Vendor,\n\n" . 
            "You've been working recently in projects of TechnoPark Corp., and earned\n" .
            "some money ({$this->balance}). We're ready to pay them immediately, if you let us know your\n".
            "bank details or the email you're using in PayPal. Actually, PayPal is more\n" .
            "convenient for us and faster for you.\n\n" .
            "Full list of tasks completed and payments made is below:\n\n";
        
        foreach ($this as $payment) {
            $text .= sprintf(
                "%10s: %12s   %s\n",
                FaZend_Date::make($payment->created)->get(Zend_Date::DATE_SHORT), 
                $payment->amount,
                ($payment->reason ? "[{$payment->context}/{$payment->reason}] " : false) .
                $payment->details
            );
        }
        
        $text .= 
        "\nTotal to pay now: {$this->balance}. Please reply to this email with the information\n" .
        "required, and we will proceed with the payment. If we made some mistakes in the\n" .
        "calculations above, please let us know and we fix them. Thanks for working with us!\n";
            
        return $text;
    }
    
    /**
     * Calculate balance
     *
     * @return FaZend_Bo_Money
     **/
    protected function _getBalance() 
    {
        return thePayment::getStatementBalance($this);
    }
    
    /**
     * Calculate total volume
     *
     * @return FaZend_Bo_Money
     **/
    protected function _getVolume() 
    {
        return thePayment::getStatementVolume($this);
    }
    
    /**
     * Get rate of supplier, if possible
     *
     * @return FaZend_Bo_Money|null
     */
    protected function _getRate() 
    {
        $registry = Model_Artifact::root()->supplierRegistry;
        if (isset($registry[$this->supplier])) {
            return $registry[$this->supplier]->rate;
        }
        return null;
    }    
    
    /**
     * Payment exists?
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param integer Id of the payment
     * @return boolean
     */
    public function offsetExists($id) 
    {
        $payment = $this->offsetGet($id);
        return $payment->exists();
    }

    /**
     * Get one statement
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param integer Id of the payment
     * @return boolean
     */
    public function offsetGet($id) 
    {
        return new thePayment(intval($id));
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     */
    public function offsetSet($email, $value) 
    {
        FaZend_Exception::raise(
            'StatementException', 
            "Statements are not editable directly"
        );
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     */
    public function offsetUnset($email) 
    {
        FaZend_Exception::raise(
            'StatementException', 
            "Statements are not editable directly"
        );
    }

    /**
     * Return current element
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function current() 
    {
        return $this->_getRowset()->current();
    }
    
    /**
     * Return next
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function next() 
    {
        return $this->_getRowset()->next();
    }
    
    /**
     * Return key
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function key() 
    {
        return $this->_getRowset()->key();
    }
    
    /**
     * Is valid?
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return boolean
     */
    public function valid() 
    {
        return $this->_getRowset()->valid();
    }
    
    /**
     * Rewind
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function rewind() 
    {
        return $this->_getRowset()->rewind();
    }
    
    /**
     * Count them
     * 
     * The method is required by Countable interface, don't delete it.
     *
     * @return theStatement
     */
    public function count() 
    {
        return $this->_getRowset()->count();
    }
    
    /**
     * Get rowset with payments
     *
     * @return thePayment[]
     **/
    protected function _getRowset() 
    {
        if (!isset($this->_rowset))
            $this->_rowset = thePayment::retrieveByStatement($this);
        return $this->_rowset;
    }

}
