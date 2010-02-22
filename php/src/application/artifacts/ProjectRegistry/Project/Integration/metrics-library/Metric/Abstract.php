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
 * Abstract project metric
 *
 * You can access any metric like this:
 *
 * <code>
 * $metric->value; // current value
 * $metric->default; // default target to be reached
 * $metric->objective; // target to be reached, equals to 'default' if not changed
 * $metric->delta; // the difference between target and current value
 * </code>
 * 
 * @package Artifacts
 */
abstract class Metric_Abstract
    implements Model_Artifact_Stateless, Model_Artifact_Passive
{

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
    private $_name;

    /**
     * Value of the metric, loaded latest
     *
     * @var numeric
     */
    private $_value = null;
    
    /**
     * Default target of the metric
     *
     * @var numeric
     */
    private $_default;
    
    /**
     * Set of options
     *
     * @var array
     * @see cloneByPattern()
     */
    protected $_options;
    
    /**
     * List of patterns
     *
     * Associative array where keys are REGEX patterns and values are
     * coma-separated lists of variables to be assigned from REGEX found
     * places. For example:
     *
     * <code>
     *  '/codes/(\d{4})/(\w{4})' => 'digits, letters'
     * </code>
     *
     * In this example, inside reload() you will be able to access vars
     * by means of $this->_getOption('digits') or $this->_getOption('letters').
     *
     * @var array
     */
    protected $_patterns;
    
    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        // you should override this method in child class
        FaZend_Exception::raise(
            'MetricIsNotOverriden', 
            'Metric ' . get_class($this) . 'does not override reload(), it is wrong'
        );
    }
        
    /**
     * Is it loaded?
     *
     * @return boolean
     */
    public final function isLoaded()
    {
        return isset($this->_value);
    }
        
    /**
     * Save the holder of the metric
     *
     * @param theMetrics Owner of this metric
     * @return void
     * @see Model_Artifact::_attach()
     */
    public final function setMetrics(theMetrics $metrics)
    {
        $this->_project = $metrics->ps()->parent;
    }
        
    /**
     * Save the name of this metric
     *
     * @param string Name of the metric
     * @return void
     **/
    public final function setName($name)
    {
        $this->_name = $name;
    }
        
    /**
     * Save the objective
     *
     * @param integer Objective to define for the metric
     * @return $this
     */
    public final function setObjective($objective)
    {
        if (!$this->visible) {
            FaZend_Exception::raise(
                'Metrics_VisibilityException', 
                'Metric (' . get_class($this) . ') is not visible, you canot set its objective'
            );
        }
        logg('set %s to %d', $this->name, $objective);
        $this->_project->objectives->setObjective(
            $this->name, 
            $objective
        );
        return $this;
    }
        
    /**
     * Simplified getter, dispatcher
     *
     * @param string Name of the property
     * @return numeric
     * @throws Metrics_ReloadingExceptions
     * @throws Metrics_AccessException
     **/
    public final function __get($name)
    {
        switch ($name) {
            case 'name':
                return $this->_name;
                
            case 'suffix':
                if (strpos($this->_name, '/') === false) {
                    return $this->_name;
                }
                return substr($this->_name, strrpos($this->_name, '/') + 1);
                    
            case 'id':
                return Model_Pages_Encoder::encode($this->_name);

            case 'value':
                if (!$this->isLoaded()) {
                    FaZend_Exception::raise(
                        'Metrics_ReloadingException', 
                        'Metric ' . get_class($this) . 
                        '[' . $this->name . '] is not reloaded by reload(), why?'
                    );
                }
                return $this->_value;
                
            case 'default':
                if (!isset($this->_default)) {
                    return null;
                }
                return $this->_default;
                
            // target is set in objectives, if set
            case 'objective':
                if (isset($this->_project->objectives[$this->_name])) {
                    return $this->_project->objectives[$this->_name]->value;
                }
                return $this->_default;
                
            case 'delta':
                if (isset($this->_default)) {
                    return $this->objective - $this->_value;
                }
                return null;

            // if this metric doesn't have DEFAULT - we can't make it visible
            // in objective and nobody can set it's "objective" value
            case 'visible':
                return isset($this->_default);
        }
        
        FaZend_Exception::raise(
            'Metrics_AccessException', 
            'You can GET only declared properties of a metric (' . get_class($this) . '::' . $name . ')'
        );
    }
        
    /**
     * Simplified setter, dispatcher
     *
     * @param string Name of the property
     * @param integer Value to save
     * @return void
     **/
    public final function __set($name, $value)
    {
        validate()
            ->true(
                is_null($value) || is_numeric($value), 
                "You can only save NUMERIC values to metrics, '{$value}' provided"
            );
        
        switch ($name) {
            case 'objective':
                return $this->setObjective($value);
            case 'value':
                return $this->_value = $value;
            case 'default':
                return $this->_default = $value;
            default:    
                FaZend_Exception::raise(
                    'MetricAccessException', 
                    'You can SET only declared properties of a metric (' . 
                    get_class($this) . '::' . $name . ')'
                );
        }
    }
    
    /**
     * The metric matches this pattern?
     *
     * @param string Regex pattern
     * @return boolean
     **/
    public final function isMatched($pattern)
    {
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
     * @throws MetricPatternMismatch
     **/
    public final function cloneByPattern($pattern)
    {
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

        FaZend_Exception::raise(
            'MetricPatternMismatch', 
            'Cannot clone ' . get_class($this) . ' with this pattern: "' . $pattern . '"'
        );
    }

    /**
     * Return one single option or NULL if it's not set
     *
     * @param string Name of option
     * @return mixed|null
     **/
    protected final function _getOption($name)
    {
        if (isset($this->_options[$name]))
            return $this->_options[$name];
        return null;
    }

    /**
     * Create work package according to this metric information
     *
     * @return theWorkPackage|null The work package to achieve this metric target or null if not necessary to achieve
     * @see theWbs::_findWorkPackage()
     */
    public final function getWorkPackage()
    {
        $metrics = array();
        $wp = $this->_derive($metrics);
        foreach ($metrics as $name) {
            if ($name[0] == '.') {
                $name = $this->name . substr($name, 1);
            }
            $this->_project->wbs[$name];
        }
        return $wp;
    }
        
    /**
     * Split work package onto activities
     *
     * @param theActivities The list of activities to be extended
     * @return void
     */
    public final function split(theActivities $list)
    {
        $wp = $this->_project->wbs[$this->_name];
        $activity = theActivity::factory($this->_project->activityList->activities, $this->_name, '1')
            ->setSow($wp->title)
            ->setCost($wp->cost);
        $list[] = $activity;
        
        $slice = $list->getSliceByWp($wp);
        $this->_split($slice);
    }
    
    /**
     * Derive the work package from this metric
     *
     * @param string[] List of other metrics to consider
     * @return theWorkPackage|null
     */
    protected function _derive(array &$metrics) 
    {
        // override it
        return null;
    }
    
    /**
     * Split, by slice provided
     *
     * @param Slice_Plugin_Abstract Slice to use
     * @return void
     **/
    protected function _split(Slice_Plugin_Abstract $slice)
    {
        // override it
        return null;
    }
    
    /**
     * Create work package, internal helper
     *
     * @param string|integer|FaZend_Bo_Money Cost, param for FaZend_Bo_Money::__construct()
     * @param string Title of work package
     * @return theWorkPackage
     **/
    protected final function _makeWp($cost, $title)
    {
        if (!($cost instanceof FaZend_Bo_Money))
            $cost = new FaZend_Bo_Money($cost);
        return new theWorkPackage($this->_name, $cost, $title);
    }
    
    /**
     * Make sure the sub-metric with this pattern is loaded
     *
     * @return void
     **/
    protected function _pingPattern($pattern)
    {
        $this->_project->metrics[$this->_name . '/' . $pattern];
    }
        
}
