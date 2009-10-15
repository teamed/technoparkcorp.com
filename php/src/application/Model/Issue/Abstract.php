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
 * One abstract issue in issue-tracking system
 *
 * @package Model
 */
abstract class Model_Issue_Abstract {

    /**
     * Tracker
     * 
     * @var Model_Issue_Tracker_Abstract
     */
    protected $_tracker = null;

    /**
     * Code of the issue, unique in the tracker
     * 
     * @var string
     */
    protected $_code;
    
    /**
     * Changelog
     *
     * @var Model_Issue_Changelog_Changelog
     */
    protected $_changelog;

    /**
     * Unique ID of the ticket in tracker
     *
     * @var integer
     */
    protected $_id = null;
    
    /**
	 * Constructor
     *
     * @param Model_Issue_Tracker_Abstract Tracker instance
     * @param string Unique code of the issue
     * @return void
     */
	public function __construct(Model_Issue_Tracker_Abstract $tracker, $code) {
	    $this->_tracker = $tracker;
	    $this->_code = $code;
    }

    /**
	 * Destructor
     *
     * @return void
     */
	public function __destruct() {
	    $this->_saveChangelog();
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('Model_Issue_PropertyOrMethodNotFound', 
            "Can't find what is '$name'");        
    }

    /**
     * Get changelog for this issue
     *
     * @return Model_Issue_Changelog_Changelog
     **/
    protected function _getChangelog() {
        if (isset($this->_changelog))
            return $this->_changelog;
            
        $this->_changelog = new Model_Issue_Changelog_Changelog();
        
        // load it with real-life data from tracker
        if ($this->exists())
            $this->_loadChangelog();
            
        // make sure the code is set properly
        $this->_changelog->set('summary', $this->_code);

        return $this->_changelog;
    }

    /**
     * This issue really exist in tracker now?
     *
     * @return integer ID in tracker
     **/
    abstract public function exists();

    /**
     * Load changelog
     *
     * @return void
     **/
    abstract protected function _loadChangelog();

    /**
     * Save changelog
     *
     * @return void
     **/
    abstract protected function _saveChangelog();

}
