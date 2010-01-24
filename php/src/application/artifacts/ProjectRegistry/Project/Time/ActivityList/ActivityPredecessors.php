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
 * List of activity predecessors
 *
 * @package Artifacts
 */
class theActivityPredecessors implements ArrayAccess, Iterator, Countable
{

    /**
     * Predecessors
     *
     * @var ArrayIterator
     **/
    protected $_predecessors;
    
    /**
     * Constructor
     *
     * @return void
     **/
    public function __construct() 
    {
        $this->_predecessors = new ArrayIterator();
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $predecessors = array();
        foreach ($this as $p)
            $predecessors[] = (string)$p;
        return (string)implode('; ', $predecessors);
    }
    
    /**
     * Add precessor
     *
     * @param theActivity Predecessor
     * @param string Type of link
     * @param integer Lag in calendar days
     * @return $this
     */
    public function add(theActivity $predecessor, $type = theActivityPredecessor::FINISH_TO_START, $lag = 0)
    {
        $this[] = theActivityPredecessor::factory($predecessor, $type, $lag);
        return $this;
    }
    
    /**
     * Calculate start of this activity
     *
     * @param theActivity We shall use it as a basis, to calculate ITS start
     * @return Zend_Date
     **/
    public function calculateStart(theActivity $activity)
    {
        $start = new FaZend_Date();
        foreach ($this as $pred) {
            $predStart = $pred->calculateStart($activity);
            if ($predStart->isLater($start))
                $start = $predStart;
        }
        return $start;
    }
    
    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function rewind() 
    {
        return $this->_predecessors->rewind();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function next() 
    {
        return $this->_predecessors->next();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function key() 
    {
        return $this->_predecessors->key();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function valid() 
    {
        return $this->_predecessors->valid();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function current() 
    {
        return $this->_predecessors->current();
    }

    /**
     * Method from Countable interface
     *
     * @return void
     **/
    public function count() 
    {
        return $this->_predecessors->count();
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetGet($name) 
    {
        return $this->_predecessors->offsetGet($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetSet($name, $value) 
    {
        return $this->_predecessors->offsetSet($name, $value);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetExists($name) 
    {
        return $this->_predecessors->offsetExists($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetUnset($name) 
    {
        return $this->_predecessors->offsetUnset($name);
    }

}
