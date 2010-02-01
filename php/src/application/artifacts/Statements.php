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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Collection of fin statements
 *
 * @package Artifacts
 */
class theStatements implements ArrayAccess, Iterator, Countable, Model_Artifact_Interface
{
    
    /**
     * Rowset from theStatement
     *
     * @var theStatement[]
     **/
    protected $_rowset = null;

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
        
        FaZend_Exception::raise(
            'Statements_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }

    /**
     * Calculate balance
     *
     * @return FaZend_Bo_Money
     **/
    protected function _getBalance() 
    {
        return thePayment::getBalance($this);
    }
    
    /**
     * Calculate total volume
     *
     * @return FaZend_Bo_Money
     **/
    protected function _getVolume() 
    {
        return thePayment::getVolume($this);
    }
    
    /**
     * Statement exists?
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Name of the statement (email)
     * @return boolean
     */
    public function offsetExists($email) 
    {
        try {
            $this->offsetGet($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get one statement
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Name of the statement (email)
     * @return boolean
     */
    public function offsetGet($email) 
    {
        return theStatement::findBySupplier($email);
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
            'StatementsException', 
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
            'StatementsException', 
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
     * Returns rowset with statements
     *
     * @return theStatement[]
     **/
    protected function _getRowset() 
    {
        if (!isset($this->_rowset))
            $this->_rowset = theStatement::retrieveAll();
        return $this->_rowset;
        
    }
    
}
