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
 * Single work package
 *
 * @package Artifacts
 */
class theWorkPackage implements Model_Artifact_Stateless {
    
    /**
     * WBS, holder of this work package
     *
     * @var theWbs
     */
    protected $wbs;
    
    /**
     * Config of the WP
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Create new work package from INI file
     *
     * @param string Code of the work package
     * @param Zend_Config_Ini INI file with configuration
     * @return void
     **/
    public function __construct($code, Zend_Config_Ini $config) {
        $this->_code = $code;
        $this->_config = $config;
    }
    
    /**
     * Set WBS
     *
     * @param theWbs
     * @return void
     */
    public function setWbs(theWbs $wbs) {
        $this->_wbs = $wbs;
    }
    
    /**
     * Getter
     *
     * @return mixed
     **/
    public function __get($name) {
        switch ($name) {
            case 'cost':
                return new Model_Cost($this->_wbs->translateIni($this->_config->cost, true));
            case 'sow':
                return $this->_wbs->translateIni($this->_config->sow);
            case 'code':
                return $this->_code;
        }
    }
    
    /**
     * Get list of all activities
     *
     * @param theActivitySplitter Splitter, the dispatcher
     * @return void
     */
    public function split(theActivitySplitter $splitter) {
        $activities = new theActivities();
        
        // create first single activity
        $splitter->split($this, 'single', new Zend_Config(array()), $activities);

        // parse this activity by all modules from INI file
        foreach ($this->_config->split as $key=>$config) {
            $splitter->split($this, $key, $config, $activities);
        }
        
        $splitter->append($activities);
    }
    
}
