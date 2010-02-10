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
 * @version $Id: Supplier.php 637 2010-02-09 09:49:56Z yegor256@yahoo.com $
 *
 */

/**
 * Collection of sales sheets
 *
 * @package Artifacts
 */
class theSheetsCollection implements ArrayAccess, Iterator, Countable
{

    /**
     * Array of sheets
     *
     * @var theSheet[]
     */
    protected $_sheets;
    
    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct() 
    {
        $this->_sheets = new ArrayIterator();
    }

    /**
     * Initialize autoloader, to be called from bootstrap
     *
     * @return void
     * @see bootstrap.php
     */
    public static function initAutoloader() 
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Sheet_');
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    get_include_path(),
                    dirname(__FILE__) . '/sheets-collection'
                )
            )
        );
        require_once dirname(__FILE__) . '/sheets-collection/Sheet/Abstract.php';
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Opportunity_PropertyOrMethodNotFound
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
            'SheetsCollection_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function rewind() 
    {
        return $this->_sheets->rewind();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function next() 
    {
        return $this->_sheets->next();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function key() 
    {
        return $this->_sheets->key();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function valid() 
    {
        return $this->_sheets->valid();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function current() 
    {
        return $this->_sheets->current();
    }

    /**
     * Method from Countable interface
     *
     * @return void
     **/
    public function count() 
    {
        return $this->_sheets->count();
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetGet($name) 
    {
        return $this->_sheets->offsetGet($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetSet($name, $value) 
    {
        validate()->true($value instanceof Sheet_Abstract);
        return $this->_sheets->offsetSet($name, $value);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetExists($name) 
    {
        return $this->_sheets->offsetExists($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetUnset($name) 
    {
        return $this->_sheets->offsetUnset($name);
    }

}
