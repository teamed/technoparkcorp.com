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
 * @version $Id: Deliverables.php 716 2010-02-21 14:20:35Z yegor256@yahoo.com $
 *
 */

/**
 * One attribute of one deliverable
 *
 * @package Artifacts
 */
class theDeliverableAttribute
{
    
    /**
     * List of values
     *
     * @var theDeliverableAttributeValue[]
     */
    protected $_values;
    
    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct()
    {
        $this->_values = new ArrayIterator();
    }
    
    /**
     * Get string
     *
     * @return string
     */
    public function __toString() 
    {
        return strval($this->value);
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string|array
     * @throws DeliverableAttribute_InvalidPropertyException
     */
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
        
        FaZend_Exception::raise(
            'DeliverableAttribute_InvalidPropertyException', 
            "Can't find what is '{$name}' in " . get_class($this)
        );        
    }

    /**
     * Add new value
     *
     * @param mixed Value
     * @param Zend_Date When this value was set?
     * @param string Email of author of change
     * @param string Log, if necessary
     * @return $this
     */
    public function add($value, Zend_Date $date = null, $author = null, $log = '') 
    {
        if (is_null($date)) {
            $date = Zend_Date::now();
        }
        $this->_values[] = new theDeliverableAttributeValue($value, $date, $author, $log);
        return $this;
    }
    
    /**
     * Latest value equals to...
     *
     * @param mixed Value to compare with
     * @return boolean
     */
    public function equalsTo($val)
    {
        $value = $this->value;
        if (!is_null($value)) {
            $value = $value->value;
        }
        return $value == $val;
    }
    
    /**
     * Ever had this value?
     *
     * @param mixed Value to compare with
     * @return boolean
     */
    public function wasEqualTo($val)
    {
        foreach ($this->_values as $value) {
            if ($value->value == $val) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Is it true?
     *
     * @return boolean
     */
    public function isTrue()
    {
        return $this->equalsTo(true);
    }
    
    /**
     * Was it ever true?
     *
     * @return boolean
     */
    public function wasTrue()
    {
        return $this->wasEqualTo(true);
    }
    
    /**
     * Get latest value
     *
     * @return theDeliverableAttributeValue
     */
    protected function _getValue() 
    {
        $latest = null;
        foreach ($this->_values as $value) {
            if (is_null($latest) || $value->date->isLater($latest->date)) {
                $latest = $value;
            }
        }
        return $latest;
    }
    
    // /**
    //  * Method from Iterator interface
    //  *
    //  * @return void
    //  */
    // public function rewind() 
    // {
    //     $this->_values->rewind();
    // }
    // 
    // /**
    //  * Method from Iterator interface
    //  *
    //  * @return void
    //  */
    // public function key() 
    // {
    //     return $this->_values->key();
    // }
    // 
    // /**
    //  * Method from Iterator interface
    //  *
    //  * @return void
    //  */
    // public function current() 
    // {
    //     return $this->_values->current();
    // }
    // 
    // /**
    //  * Method from Iterator interface
    //  *
    //  * @return void
    //  */
    // public function next() 
    // {
    //     $this->_values->next();
    // }
    // 
    // /**
    //  * Method from Iterator interface
    //  *
    //  * @return void
    //  */
    // public function valid() 
    // {
    //     return $this->_values->valid();
    // }
    
}
