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
 * One activity
 *
 * @package Artifacts
 */
class theActivity {

    const SEPARATOR = '.';

    const DEFAULT_PRICE_PER_HOUR_USD = 2;

    /**
     * Holder of this activity
     *
     * @var theActivities
     */
    protected $_activities;

    /**
     * Work package it came from, code
     *
     * @var string
     */
    protected $_wp;

    /**
     * Code of activity inside this WP
     *
     * @var integer|string
     */
    protected $_code;

    /**
     * Statement of work
     *
     * @var string
     */
    protected $_sow;

    /**
     * Cost we can spend on it, our estimate
     *
     * @var Model_Cost
     */
    protected $_cost;
    
    /**
     * Assigned performer (doesn't mean that he/she AGREED to perform the activity)
     *
     * @var theStatekholder
     */
    protected $_performer;
    
    /**
     * Criteria of final
     *
     * @var theActivityCriteria
     */
    protected $_criteria;
    
    /**
     * Controller, acceptor of the activity
     *
     * @var theStatekholder
     */
    protected $_acceptor;
    
    /**
     * List of predecessors
     *
     * @var theActivityPredecessors
     */
    protected $_predecessors;
    
    /**
     * Construct it
     *
     * @param theActivities Holder of this activity
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return void
     **/
    public function __construct(theActivities $activities, $wp, $code) {
        $this->_activities = $activities;
        $this->_wp = (string)$wp;
        $this->_code = (string)$code;
    }

    /**
     * Factory method
     *
     * @param theActivities Holder of this activity
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return theActivity
     **/
    public static function factory(theActivities $activities, $wp, $code) {
        return new theActivity($activities, $wp, $code);
    }

    /**
     * Call plugin
     *
     * @param string Name of plugin, lowercase first letter
     * @param array List of arguments
     * @return Activity_Plugin_Abstract
     **/
    public function __call($method, array $args) {
        require_once dirname(__FILE__) . '/activity-plugins/Abstract.php';
        $plugin = Activity_Plugin_Abstract::factory($method, $this);
        if (method_exists($plugin, 'execute'))
            return call_user_func_array(array($plugin, 'execute'), $args);
        return $plugin;
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('Activity_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in '{$this->name}' activity");
    }

    /**
     * Set SOW
     *
     * @param string Statement of work for the activity
     * @return $this
     */
    public function setSow($sow) {
        $this->_sow = $sow;
        return $this;
    }

    /**
     * Set cost, our estimate
     *
     * @param Model_Cost Cost estimate
     * @return $this
     */
    public function setCost(Model_Cost $cost) {
        $this->_cost = clone $cost;
        return $this;
    }

    /**
     * Set performer
     *
     * @param theStakeholder Stakeholder to be assigned here
     * @return $this
     */
    public function setPerformer(theStakeholder $person) {
        $this->_performer = $person;
        return $this;
    }

    /**
     * Is it a milestone
     *
     * @return boolean
     */
    public function isMilestone() {
        return !isset($this->_cost);
    }

    /**
     * Activity belongs to this work package?
     *
     * @param theWorkPackage
     * @return boolean
     */
    public function belongsTo(theWorkPackage $wp) {
        return $wp->code == $this->_wp;
    }

    /**
     * Activity equals to another one?
     *
     * @param theActivity
     * @return boolean
     */
    public function equalsTo(theActivity $activity) {
        return $activity->name === $this->name;
    }

    /**
     * Unique name of the activity
     *
     * @return string
     */
    protected function _getName() {
        return $this->_wp . self::SEPARATOR . $this->_code;
    }

    /**
     * Project of this activity
     *
     * @return theProject
     */
    protected function _getProject() {
        return $this->_activities->getProject();
    }

    /**
     * Unique alnum ID of the activity
     *
     * @return string
     */
    protected function _getId() {
        return Model_Pages_Encoder::encode($this->name);
    }

    /**
     * Description of activity
     *
     * @return string
     * @todo Implement it through criteria
     */
    protected function _getDescription() {
        return $this->sow;
    }

    /**
     * Doc name which is responsible for the description of this activity
     *
     * @return string
     */
    protected function _getDoc() {
        return 'projects/' . $this->project->name . '/Scope/WBS/' . str_replace(theMetrics::SEPARATOR, '-', $this->code);
    }
    
    /**
     * Get estimated start of the activity
     *
     * @return Zend_Date
     */
    protected function _getStart() {
        return $this->predecessors->calculateStart($this);
    }

    /**
     * Get estimated finish of the activity
     *
     * @return Zend_Date
     * @todo Shall be done properly and shall take into account performer and his/her price
     */
    protected function _getFinish() {
        $finish = $this->start;
        $finish->add($this->duration, Zend_Date::DAY);
        return $finish;
    }
    
    /**
     * Get estimated duration of the activity
     *
     * @return int In calendar days
     * @todo Shall be done properly and shall take into account performer and his/her price
     */
    protected function _getDuration() {
        // if it's a milestone or just unkown cost
        if (!$this->cost)
            return 0;
        
        return ceil(($this->cost->usd / self::DEFAULT_PRICE_PER_HOUR_USD) / 4);
    }
    
    /**
     * Get criteria
     *
     * @return theActivityCriteria The criteria of closure
     */
    protected function _getCriteria() {
        if (!isset($this->_criteria))
            $this->_criteria = new theActivityCriteria();
        return $this->_criteria;
    }

    /**
     * Get predecessors
     *
     * @return theActivityPredecessors List of predecessors
     */
    protected function _getPredecessors() {
        if (!isset($this->_predecessors))
            $this->_predecessors = new theActivityPredecessors();
        return $this->_predecessors;
    }

}
