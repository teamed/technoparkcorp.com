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
 * @package Artifacts
 */
abstract class theMtcAbstract extends FaZend_StdObject {

    /**
     * Project
     *
     * @var theProject
     */
    protected $_project;
    
    /**
     * Title of the metric
     *
     * @var string
     */
    protected $_title;

    /**
     * Default value
     *
     * @var integer
     */
    protected $_default = 0;

    /**
     * Is it visible in objectives?
     *
     * @var boolean
     */
    protected $_visible = false;

    /**
     * Create artifact using project
     *
     * @param theProject Holder of this collection
     * @return void
     **/
    public function __construct(theProject $project) {
        $this->_project = $project;
        $this->_init();
    }
        
    /**
     * Get value of the metric
     *
     * @return integer
     **/
    public function getValue() {
        return $this->_calculate();
    }
        
    /**
     * Perform real calculation of the metric
     *
     * @return integer
     **/
    abstract protected function _calculate();
        
    /**
     * Initialize this metric
     *
     * @return void
     **/
    protected function _init() {
        // to be overriden...
    }
        
    /**
     * Access point to other metrics in the project
     *
     * @return theMetrics
     **/
    protected function _metrics() {
        return $this->_project->metrics;
    }
        
}
