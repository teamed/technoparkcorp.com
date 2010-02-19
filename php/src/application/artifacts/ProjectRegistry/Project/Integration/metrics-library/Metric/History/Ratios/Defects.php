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
 * @version $Id: Defects.php 694 2010-02-17 13:41:23Z yegor256@yahoo.com $
 *
 */

/**
 * Amount of defects per one...
 * 
 * @package Artifacts
 */
class Metric_History_Ratios_Defects extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * "history/rations/defects/per/class" = defects to be found per one class
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/^per\/(functional)-(\w+)$/' => 'deliverable, level',
        '/^per\/(.*)$/' => 'deliverable',
    );

    /**
     * Load this metric
     *
     * @return void
     * @throws Metric_History_Ratios_Defects_InvalidDeliverableException
     * @throws Metric_History_Ratios_Defects_InvalidLevelException
     */
    public function reload()
    {
        $deliverable = $this->_getOption('deliverable');
        $level = $this->_getOption('level');
        
        if (!$deliverable) {
            $this->value = 0;
            return;
        }
        
        switch (true) {
            case $deliverable == 'class':
                $this->value = 3;
                break;
            case $deliverable == 'functional':
                $this->value = 1;
                break;
            case ($deliverable == 'functional') && ($level):
                switch ($level) {
                    case 'first';
                        $this->value = 3;
                        break;
                    default:
                        FaZend_Exception::raise(
                            'Metric_History_Ratios_Defects_InvalidLevelException',
                            "Unsupported level: '{$level}'"
                        );
                }
                break;
            case 'qos':
                $this->value = 10;
                break;
            default:
                FaZend_Exception::raise(
                    'Metric_History_Ratios_Defects_InvalidDeliverableException',
                    "Can't find what is '{$deliverable}'"
                );
        }
    }
                 
}
