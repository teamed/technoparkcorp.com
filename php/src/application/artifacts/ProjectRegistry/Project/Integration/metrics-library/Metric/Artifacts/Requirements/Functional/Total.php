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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Total number of functional requiremnts
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Requirements_Functional_Total extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/level\/(\w+)/' => 'level',
    );

    /**
     * Level code
     * 
     * @var array
     */
    protected $_levelCodes = array(
        'first' => 0,
        'second' => 1,
        'third' => 2,
        'forth' => 3,
    );

    /**
     * Get all level codes
     *
     * @return string
     */
    public function getLevelCodes() 
    {
        return array_keys($this->_levelCodes);
    }
    
    /**
     * Get one level code, by it's mnemo
     *
     * @param string Mnemo of the level, like "first", "second", etc.
     * @return string
     * @throws Metric_Artifacts_Requirements_Functional_Total_InvalidMnemoException
     */
    public function getLevelCode($mnemo) 
    {
        if (!array_key_exists($mnemo, $this->_levelCodes)) {
            FaZend_Exception::raise(
                'Metric_Artifacts_Requirements_Functional_Total_InvalidMnemoException',
                "Can't find what is '{$mnemo}'"
            );
        }
        return $this->_levelCodes[$mnemo];
    }
    
    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        // we can't calculate metrics here if deliverables are not loaded
        if (!$this->_project->deliverables->isLoaded()) {
            $this->_project->deliverables->reload();
        }
            
        $level = $this->_getOption('level');
        if ($level) {
            $this->value = 0;
            foreach ($this->_project->deliverables->functional as $requirement) {
                if ($this->getLevelCode($level) == $requirement->getLevel()) {
                    $this->value += 1;
                }
            }
            return;
        }
        
        $this->value = count($this->_project->deliverables->functional);
    }
            
}
