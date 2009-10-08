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
 * Abstract plugin
 * 
 * @package Slice_Plugin
 */
abstract class Slice_Plugin_Abstract implements Iterator {

    /**
     * List of actitivites
     *
     * @var theActivities|array
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
    public static function factory($name, $activities) {
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
    public function __construct($activities) {
        $this->_activities = $activities;
    }
        
    /**
     * Call sub-slice
     *
     * @return Slice_Plugin_Abstract
     **/
    public function __call($method, array $args) {
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
    public function delete(theActivity $toKill) {
        return $this->_activities->delete($toKill);
    }
    
    /**
     * Create one new activity
     *
     * @param string Code of new activity
     * @return theActivity
     **/
    public function add($code) {
        return $this->_activities->add($code);
    }
    
    /**
     * Iterator::current()
     *
     * @return theActivity
     **/
    public function current() {
        return $this->_activities->current();
    }
    
    /**
     * Iterator::key()
     *
     * @return theActivity
     **/
    public function key() {
        return $this->_activities->key();
    }
    
    /**
     * Iterator::next()
     *
     * @return theActivity
     **/
    public function next() {
        return $this->_activities->next();
    }
    
    /**
     * Iterator::rewind()
     *
     * @return theActivity
     **/
    public function rewind() {
        $this->_activities->rewind();
        while ($this->_activities->current() && !$this->_isInside($this->_activities->current())) {
            $this->_activities->next();
        }
    }
    
    /**
     * Iterator::valid()
     *
     * @return theActivity
     **/
    public function valid() {
        return $this->_activities->valid();
    }
    
    /**
     * What activities in the global list are here, in this slice?
     *
     * @param theActivity Activity to check
     * @return boolean
     **/
    protected function _isInside(theActivity $activity) {
        return true;
    }
        
}