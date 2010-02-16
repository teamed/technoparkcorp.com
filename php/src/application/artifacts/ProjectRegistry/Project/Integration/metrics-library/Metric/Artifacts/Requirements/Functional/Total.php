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
            
        if ($this->_getOption('level')) {
            validate()
                ->true(isset($this->_pricePerRequirement[$this->_getOption('level')]));

            $this->value = 0;
            foreach ($this->_project->deliverables->functional as $requirement) {
                if ($requirement->getLevel() == $this->_levelCodes[$this->_getOption('level')]) {
                    $this->value++;
                }
            }
            
            $increment = pow($this->_project->metrics['artifacts/requirements/functional/total']->objective, 1/4);
            $this->default = round(pow($increment, 1+$this->_levelCodes[$this->_getOption('level')]));
            return;
        }
        
        $this->value = count($this->_project->deliverables->functional);
        $this->default = 200;
    }
        
    /**
     * Get work package
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage
     */
    protected function _derive(array &$metrics = array())
    {
        // we specify requirements only on some particular level
        if (!$this->_getOption('level')) {
            // instruct loader to ping these metrics/WPs
            foreach (array_keys($this->_levelCodes) as $code) {
                $metrics[] = './level/' . $code;
            }
            return null;
        }
            
        // if we already have too many requirements - skip this WP
        if ($this->delta <= 0) {
            return null;
        }
            
        // price per one requirement
        $price = new FaZend_Bo_Money(
            $this->_project
            ->metrics['history/cost/requirements/functional/level/' . $this->_getOption('level')]
            ->value
        );
            
        return $this->_makeWp(
            $price->mul($this->delta), 
            sprintf(
                'to specify +%d %s level functional requirements',
                $this->delta, 
                $this->_getOption('level'), 
                $this->value
            )
        );
    }
    
}
