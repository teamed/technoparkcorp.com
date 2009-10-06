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
     * Is it visible in objectives? Project stakeholders can set target value for the metric?
     *
     * @var boolean
     */
    public $visible = false;

    /**
     * Value of the metric, loaded latest
     *
     * @var numeric
     */
    private $_value;
    
    /**
     * Default target of the metric
     *
     * @var numeric
     */
    private $_default;
    
    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() {
        // to be overriden if necessary
        $this->value = 0;
    }
        
    /**
     * Is it loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return isset($this->_value);
    }
        
    /**
     * Save the holder of the metric
     *
     * @return void
     **/
    public function setMetrics(theMetrics $metrics) {
        $this->_project = $metrics->ps()->parent;
    }
        
    /**
     * Simplified getter, dispatcher
     *
     * @param string Name of the property
     * @return numeric
     **/
    public function __get($name) {
        
        // reload it before doing anything
        if (!$this->isLoaded())
            $this->reload();
            
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
                
            case 'target':
                // TODO: we should go into Objectives for this value!
                return $this->_default;
                
            case 'delta':
                return $this->target - $this->value;
        }
        
        FaZend_Exception::raise('MetricAccessException', "You can GET only declared properties of a metric ($name)");
    }
        
    /**
     * Simplified setter, dispatcher
     *
     * @param string Name of the property
     * @param integer Value to save
     * @return void
     **/
    public function __set($name, $value) {
        validate()->numeric($value, "You can only save NUMERIC values to metrics");
        
        switch ($name) {
            case 'value':
                $this->_value = $value;
                return;
            case 'default':
                $this->_default = $value;
                return;
        }
        
        FaZend_Exception::raise('MetricAccessException', "You can SET only declared properties of a metric ($name)");
    }
        
}
