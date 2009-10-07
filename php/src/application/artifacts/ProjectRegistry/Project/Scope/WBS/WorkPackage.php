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
    protected $_wbs;
    
    /**
     * Unique code
     *
     * @var string
     */
    protected $_code;
    
    /**
     * Cost
     *
     * @var Model_Cost
     */
    protected $_cost;
    
    /**
     * Title
     *
     * @var string
     */
    protected $_title;
    
    /**
     * Create new work package
     *
     * @param string Code of the work package
     * @param Model_Cost Cost of work package
     * @param string Title of it
     * @return void
     **/
    public function __construct($code, Model_Cost $cost, $title) {
        $this->_code = $code;
        $this->_cost = $cost;
        $this->_title = $title;
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
                return $this->_cost;
            case 'title':
                return $this->_title;
            case 'code':
                return $this->_code;
        }
    }
    
}
