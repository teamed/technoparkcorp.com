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
        '/level\/(\w+)\/(\w+)/' => 'level, status',
    );

    /**
     * Level code
     */
    protected $_levelCode = array(
        'first' => 0,
        'second' => 1,
        'third' => 2,
        'forth' => 3,
    );

    /**
     * Price per each requirement on some level
     *
     * @var array
     */
    protected $_pricePerRequirement = array(
        'first' => '45 USD',
        'second' => '10 USD',
        'third' => '4 USD',
        'forth' => '2 USD'
    );

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
            
        if ($this->_getOption('level') !== null) {
            $max = max(array_keys($this->_pricePerRequirement));
            validate()
                ->true(isset($this->_pricePerRequirement[$this->_getOption('level')]));

            $this->_value = 0;
            foreach ($this->_project->deliverables->functional as $requirement) {
                if (substr_count($requirement, '.') == $this->_levelCode[$this->_getOption('level')])
                    $this->_value++;
            }
            
            $increment = pow($this->_project->metrics['artifacts/requirements/functional/total']->target, 1/4);
            $this->_default = round(pow($increment, 1+$this->_levelCode[$this->_getOption('level')]));
            return;
        }
        
        $this->_value = count($this->_project->deliverables->functional);
        $this->_default = 300;
    }
        
    /**
     * Get work package
     *
     * @return theWorkPackage
     **/
    public function getWorkPackage()
    {
        // if we already have too many requirements - skip this WP
        if ($this->delta < 0)
            return null;
            
        // we specify requirements only on some particular level
        if (!$this->_getOption('level'))
            return null;
            
        $price = $this->_pricePerRequirement[$this->_getOption('level')];
            
        return $this->_makeWp(
            $this->delta * $price, 
            sprintf(
                'Specify +%d functional requirements on %s level',
                $this->delta, 
                $this->_getOption('level'), 
                $this->value
            )
        );
    }
    
}
