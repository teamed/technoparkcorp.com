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
 * One project
 *
 * @package Artifacts
 */
class theProject extends Model_Artifact {

    /**
     * Unique name of this project, to be set from registry
     *
     * @var string
     */
    protected $_name = null;

    /**
     * List of dynamic artifacts
     * 
     * @var string
     */
    protected $_artifacts = array(
        'staffAssignments',
        'metrics',
        'workOrders',
        'metrics',
        'milestones',
    );
    
    /**
     * Cached artifacts 
     *
     * @var mixed[]
     */
    protected $_cached = array();

    /**
     * Set project name
     *
     * @param string The name
     * @return void
     **/
    public function setName($name) {
        $this->_name = $name;
    }
    
    /**
     * Get one of sub-artifacts
     *
     * @return void
     **/
    public function __get($name) {
        if (!in_array($name, $this->_artifacts)) 
            return parent::__get($name);
        $class = 'the' . ucfirst($name);
        
        if (!isset($this->_cached[$class]))
            $this->_cached[$class] = new $class($this);
        return $this->_cached[$class];
    }
    
    /**
     * Get project name
     *
     * @return string
     **/
    protected function _getName() {
        return $this->_name;
    }
    
}
