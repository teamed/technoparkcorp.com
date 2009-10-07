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
 * Abstract project metric
 *
 * You can access any metric like this:
 *
 * <code>
 * $metric->value; // current value
 * $metric->default; // default target to be reached
 * $metric->target; // target to be reached, equals to 'default' if not changed
 * $metric->delta; // the difference between target and current value
 * </code>
 * 
 * @package Artifacts
 */
abstract class Metric_Abstract
    implements Model_Artifact_Stateless, Model_Artifact_Passive {

    /**
     * Project where the metric is located
     *
     * @var theProject
     */
    protected $_project;

    /**
     * Name of this metric
     *
     * @var string
     */
    protected $_name;

    /**
     * Value of the metric, loaded latest
     *
     * @var numeric
     */
    protected $_value = null;
    
    /**
     * Default target of the metric
     *
     * @var numeric
     */
    protected $_default;
    
    /**
     * Set of options
     *
     * @var array
     */
    private $_options;
    
    /**
     * List of patterns
     *
     * @var array
     */
    protected $_patterns;
    
    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() {
        // you should override this method in child class
        FaZend_Exception::raise('MetricIsNotOverriden', 
            'Metric ' . get_class($this) . 'does not override reload(), it is wrong');
    }
        
    /**
     * Is it loaded?
     *
     * @return boolean
     **/
    public final function isLoaded() {
        return isset($this->_value);
    }
        
    /**
     * Save the holder of the metric
     *
     * @param theMetrics Owner of this metric
     * @return void
     **/
    public final function setMetrics(theMetrics $metrics) {
        $this->_project = $metrics->ps()->parent;
    }
        
    /**
     * Save the name of this metric
     *
     * @param string Name of the metric
     * @return void
     **/
    public final function setName($name) {
        $this->_name = $name;
    }
        
    /**
     * Simplified getter, dispatcher
     *
     * @param string Name of the property
     * @return numeric
     **/
    public final function __get($name) {
        
        switch ($name) {
            case 'value':
                if (!isset($this->_value)) {
                    FaZend_Exception::raise('MetricReloadingException', 
                        'Metric ' . get_class($this) . ' is not reloaded by reload(), why?');
                }
                return $this->_value;
                
            case 'default':
                if (!isset($this->_default))
                    return null;
                return $this->_default;
                
            // target is set in objectives, if set
            case 'target':
                if (isset($this->_project->objectives[$this->_name]))
                    return $this->_project->objectives[$this->_name];
                return $this->_default;
                
            case 'delta':
                if (isset($this->_default))
                    return $this->target - $this->_value;
                return null;

            // if this metric doesn't have DEFAULT - we can't make it visible
            // in objective and nobody can set it's target value
            case 'visible':
                return isset($this->_default);
        }
        
        FaZend_Exception::raise('MetricAccessException', 
            'You can GET only declared properties of a metric (' . get_class($this) . '::' . $name . ')');
    }
        
    /**
     * Simplified setter, dispatcher
     *
     * @param string Name of the property
     * @param integer Value to save
     * @return void
     **/
    public final function __set($name, $value) {
        validate()->numeric($value, "You can only save NUMERIC values to metrics");
        
        switch ($name) {
            case 'target':
                $this->_project->objectives[$this->_name] = $value;
                return;
        }
        
        FaZend_Exception::raise('MetricAccessException', 
            'You can SET only declared properties of a metric (' . get_class($this) . '::' . $name . ')');
    }
    
    /**
     * The metric matches this pattern?
     *
     * @param string Regex pattern
     * @return boolean
     **/
    public final function isMatched($pattern) {
        if (!isset($this->_patterns))
            return false;
        foreach (array_keys($this->_patterns) as $regex)
            if (preg_match($regex, $pattern))
                return true;
        return false;
    }

    /**
     * Clone metric using this pattern
     *
     * @param string Regex pattern
     * @return Metric_Abstract
     **/
    public final function cloneByPattern($pattern) {
        validate()->true(isset($this->_patterns));
        
        foreach ($this->_patterns as $regex=>$opts) {
            if (!preg_match($regex, $pattern, $matches))
                continue;
                
            $options = array();
                
            // $opts comes in with this format: "level, status, name, ..."
            $vars = explode(',', $opts);
            foreach ($vars as $i=>$var)
                $options[trim($var)] = $matches[$i+1];
            
            $className = get_class($this);
            $metric = new $className();
            $metric->_options = $options;
            return $metric;
        }

        FaZend_Exception::raise('MetricPatternMismatch', 
            'Cannot clone ' . get_class($this) . ' with this pattern: "' . $pattern . '"');
    }

    /**
     * Return one single option or NULL if it's not set
     *
     * @param string Name of option
     * @return mixed|null
     **/
    protected final function _getOption($name) {
        if (isset($this->_options[$name]))
            return $this->_options[$name];
        return null;
    }

    /**
     * Create work package according to this metric information
     *
     * @return theWorkPackage|null The work package to achieve this metric target or null if not necessary to achieve
     **/
    public function getWorkPackage() {
        // you should override this method, if necessary
        return null;
    }
        
    /**
     * Create work package, internal helper
     *
     * @param mixed Cost, param for Model_Cost::__construct()
     * @param string Title of work package
     * @return theWorkPackage
     **/
    protected final function _makeWp($cost, $title) {
        return new theWorkPackage(str_replace('_', '.', get_class($this)), new Model_Cost($cost), $title);
    }
        
}
