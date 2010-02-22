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
 * Criteria of activity closure
 *
 * @package Artifacts
 */
class theActivityCriteria implements ArrayAccess, Countable, Iterator
{

    /**
     * List of criteria
     *
     * @var string
     **/
    protected $_criteria;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct() 
    {
        $this->_criteria = new ArrayIterator();
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)implode('; ', iterator_to_array($this->_criteria));
    }
    
    /**
     * Condition to attach
     *
     * @param string Condition
     * @return $this
     */
    public function when($condition)
    {
        $args = func_get_args();
        $this[] = new theCriteriaCondition(call_user_func_array('sprintf', $args));
        return $this;
    }

    /**
     * Is it true now?
     *
     * @param theProject What is the source of metrics
     * @return boolean
     **/
    public function isTrue(theProject $project)
    {
        foreach ($this as $when) {
            if (!$when->isTrue($project))
                return false;
        }
        return true;
    }
    
    /**
     * Return criteria in HTML form
     *
     * @param theProject What is the source of metrics
     * @return string
     **/
    public function asHtml(theProject $project)
    {
        $html = '<p>All of the below shall be true:</p><ul>';

        $metrics = array();
        foreach ($this as $when) {
            $true = $when->isTrue($project);
            $html .= '<li><span class="formula">' . $when->asHtml($project, $metrics) . '</span>' . 
                ' (<span style="color: ' . ($true ? Model_Colors::GREEN : Model_Colors::RED) . '">' . 
                ($true ? 'true' : 'false') . '</span>)</li>';
        }
        $html .= '</ul>';
        
        foreach ($metrics as $var=>$metric)
            $html .= "<h2>Variable <span class='formula'><b>$var</b> = {$metric->value}</span></h2>" . 
                '<p>' . $metric->name . '</p>';
        
        return $html;
    }

    /**
     * Return a list of all metrics involved here, that affect this activity
     *
     * @return string[]
     **/
    public function getAffectors()
    {
        $metrics = array();
        foreach ($this as $when) {
            $metrics += $when->getAffectors();
        }
        return array_unique($metrics);
    }
    
    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function rewind() 
    {
        return $this->_criteria->rewind();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function next() 
    {
        return $this->_criteria->next();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function key() 
    {
        return $this->_criteria->key();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function valid() 
    {
        return $this->_criteria->valid();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     **/
    public function current() 
    {
        return $this->_criteria->current();
    }

    /**
     * Method from Countable interface
     *
     * @return void
     **/
    public function count() 
    {
        return $this->_criteria->count();
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetGet($name) 
    {
        return $this->_criteria->offsetGet($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetSet($name, $value) 
    {
        return $this->_criteria->offsetSet($name, $value);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetExists($name) 
    {
        return $this->_criteria->offsetExists($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     **/
    public function offsetUnset($name) 
    {
        return $this->_criteria->offsetUnset($name);
    }

}
