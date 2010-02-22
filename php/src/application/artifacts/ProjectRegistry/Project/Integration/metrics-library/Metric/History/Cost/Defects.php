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
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Cost of one defect to find
 * 
 * @package Artifacts
 */
class Metric_History_Cost_Defects extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/^(fix|find)\/byComponent\/(\w+)$/' => 'action, component',
    );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        $action = $this->_getOption('action');
        if (!$action) {
            $this->value = 0;
            return;
        }
        $method = '_reload' . ucfirst($action);
        $this->value = $this->$method($this->_getOption('component'));
    }
     
    /**
     * Reload by component, to FIND cost
     *
     * @param string Component
     * @return float Cost of defect, in USD
     */
    protected function _reloadFind($component) 
    {
        switch (true) {
            case $component == 'SRS':
                return 3;
            case $component == 'Design':
                return 6;
            case preg_match('/^R\d/', $component):
                return 4;
            default:
                return 1;
        }
    }
            
    /**
     * Reload by component, to FIX cost
     *
     * @param string Component
     * @return void
     */
    protected function _reloadFix($component) 
    {
        switch (true) {
            case $component == 'SRS':
                return 8.5;
            case $component == 'Design':
                return 10;
            case preg_match('/^R\d/', $component):
                return 8;
            default:
                return 6;
        }
    }
            
}
