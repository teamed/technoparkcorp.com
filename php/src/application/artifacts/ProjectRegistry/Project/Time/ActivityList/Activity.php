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
class theActivity implements Model_Artifact_Stateless, Model_Artifact_Passive {

    /**
     * Work package it came from
     *
     * @var theWorkPackage
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
     * Estimate of cost by performer
     *
     * @var Model_Cost
     */
    protected $_costEstimate;
    
    /**
     * Our estimate of duration, days
     *
     * @var integer
     */
    protected $_duration;
    
    /**
     * Estimate of duration made by performer, days
     *
     * @var integer
     */
    protected $_durationEstimate;
    
    /**
     * Assigned performer (doesn't mean that he/she AGREED to perform the activity)
     *
     * @var theStatekholder
     */
    protected $_performer;
    
    /**
     * Controller, acceptor of the activity
     *
     * @var theStatekholder
     */
    protected $_acceptor;
    
    /**
     * List of predecessors
     *
     * @var theActivityPredecessor
     */
    protected $_predecessors;
    
    /**
     * Construct it
     *
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return void
     **/
    public function __construct(theWorkPackage $wp, $code) {
        $this->_wp = $wp;
        $this->_code = $code;
    }

    /**
     * Factory method
     *
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return theActivity
     **/
    public static function factory(theWorkPackage $wp, $code) {
        return new theActivity($wp, $code);
    }

    /**
     * Is it already loaded
     *
     * @return boolean
     */
    public function isLoaded() {
        // never loaded, since we don't have information about Trac etc.
        return false;
    }

    /**
     * Reload it from Trac
     *
     * @return void
     * @todo implement it
     */
    public function reload() {
        // later...
    }
    
    /**
     * Getter dispatcher
     *
     * @return mixed
     **/
    public function __get($name) {
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        FaZend_Exception::raise('Activity_PropertyOrMethodNotFound', "Can't find what is '$name'");
    }

    /**
     * Set SOW
     *
     * @param string Statement of work
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
        $this->_cost = $cost;
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
     * Is it already assigned?
     *
     * @return boolean
     */
    public function isAssigned() {
    }

    /**
     * Is cost already estimated?
     *
     * @return boolean
     */
    public function isCostEstimated() {
    }

    /**
     * Is cost estimate already requested?
     *
     * @return boolean
     * @todo implement it
     */
    public function isCostEstimateRequested() {
    }

    /**
     * Request cost estimate from the performer
     *
     * @return void
     * @todo implement it
     */
    public function requestCostEstimate() {
    }

    /**
     * Unique name of the activity
     *
     * @return string
     */
    protected function _getName() {
        return $this->_wp->code . '.' . $this->_code;
    }

}
