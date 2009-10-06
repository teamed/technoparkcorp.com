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
 * One module
 *
 * @package Artifacts
 */
abstract class theActivitySplitterAbstract {
    
    /**
     * WBS
     *
     * @var theWbs
     */
    protected $_wbs;
    
    /**
     * Work package
     *
     * @var theWorkPackage
     */
    protected $_wp;
    
    /**
     * Configuration
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Construct it
     *
     * @return void
     **/
    public function __construct(theWbs $wbs, theWorkPackage $wp, Zend_Config $config) {
        $this->_wbs = $wbs;
        $this->_wp = $wp;
        $this->_config = $config;
    }
    
    /**
     * Split this list
     *
     * @return void
     **/
    abstract public function split(theActivities $activities);
    
    /**
     * Get project
     *
     * @return theProject
     **/
    protected function _project() {
        return $this->_wbs->ps()->parent;
    }
    
}