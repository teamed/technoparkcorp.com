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
 * Abstract plugin
 * 
 * @package Slice_Plugin
 */
abstract class Slice_Plugin_Abstract implements Iterator, Countable
{

    /**
     * List of actitivites
     *
     * @var theActivities
     */
    protected $_activities;

    /**
     * Static plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_loader;
     
    /**
     * Create new plugin, using list of activities
     *
     * @param string Name of plugin to create
     * @param theActivities Holder of this SLICE
     * @return void
     **/
    public static function factory($name, $activities)
    {
        assert($activities instanceof Slice_Plugin_Abstract || $activities instanceof theActivities);

        if (!isset(self::$_loader)) {
            self::$_loader = new Zend_Loader_PluginLoader(array('Slice_Plugin_' => dirname(__FILE__)));
        }
        // load this plugin, if possible
        $sliceName = self::$_loader->load($name);
        return new $sliceName($activities);        
    }
     
    /**
     * Save list of activities to work with
     *
     * @param theActivities Holder of this SLICE
     * @return void
     **/
    public function __construct($activities)
    {
        assert($activities instanceof Slice_Plugin_Abstract || $activities instanceof theActivities);
        
        $this->_activities = $activities;
    }
        
    /**
     * Call sub-slice
     *
     * @return Slice_Plugin_Abstract
     **/
    public function __call($method, array $args)
    {
        $slice = self::factory($method, $this);
        if (method_exists($slice, 'execute'))
            return call_user_func_array(array($slice, 'execute'), $args);
        return $slice;
    }
        
    /**
     * Delete one activity
     *
     * @param theActivity Activity to delete
     * @return void
     **/
    public function delete(theActivity $toKill)
    {
        return $this->_activities->delete($toKill);
    }
    
    /**
     * Create one new activity
     *
     * @param string Code of new activity
     * @return theActivity
     **/
    public function add($code)
    {
        return $this->_activities->add($code);
    }
    
    /**
     * Create one new milestone
     *
     * @param string Code of new milestone
     * @return theMilestone
     **/
    public function addMilestone($code)
    {
        return $this->_activities->addMilestone($code);
    }
    
    /**
     * Iterator::current()
     *
     * @return theActivity
     **/
    public function current()
    {
        return $this->_activities->current();
    }
    
    /**
     * Iterator::key()
     *
     * @return theActivity
     **/
    public function key()
    {
        return $this->_activities->key();
    }
    
    /**
     * Iterator::next()
     *
     * @return theActivity
     **/
    public function next()
    {
        return $this->_activities->next();
    }
    
    /**
     * Countable::count()
     *
     * @return integer
     **/
    public function count()
    {
        $count = 0;
        foreach ($this->_activities as $activity)
            if ($this->_isInside($activity))
                $count++;
        return $count;
    }
    
    /**
     * Iterator::rewind()
     *
     * @return theActivity
     **/
    public function rewind()
    {
        $this->_activities->rewind();
        while ($this->_activities->current() && !$this->_isInside($this->_activities->current())) {
            $this->_activities->next();
        }
    }
    
    /**
     * Iterator::valid()
     *
     * @return boolean
     **/
    public function valid()
    {
        return $this->_activities->valid();
    }
    
    /**
     * What activities in the global list are here, in this slice?
     *
     * @param theActivity Activity to check
     * @return boolean
     **/
    protected function _isInside(theActivity $activity)
    {
        return true;
    }
    
    /**
     * Find activity by code
     *
     * @param string Name/code of activity
     * @return theActivity
     **/
    protected function _findByName($name)
    {
        return $this->_activities->findByName($code);
    }
        
    /**
     * Get next available code for activity
     *
     * @param string Prefix in code
     * @return integer
     **/
    protected function _nextCode($prefix = '')
    {
        $code = 0;
        foreach ($this as $activity) {
            if (!preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $activity->code, $matches))
                continue;
            if ($matches[1] >= $code)
                $code = intval($matches[1]) + 1;
        }
        return $code;
    }
        
    /**
     * Set options if they are not yet set
     *
     * @return void
     **/
    protected function _normalizeOptions(array &$options, array $defaults)
    {
        foreach ($defaults as $key=>$value) {
            if (!isset($options[$key]))
                $options[$key] = $value;
        }
    }
        
}
