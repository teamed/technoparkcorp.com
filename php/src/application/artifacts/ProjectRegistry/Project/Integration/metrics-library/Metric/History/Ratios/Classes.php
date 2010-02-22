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
 * @version $Id: Defects.php 694 2010-02-17 13:41:23Z yegor256@yahoo.com $
 *
 */

/**
 * Amount of classes per one...
 * 
 * @package Artifacts
 */
class Metric_History_Ratios_Classes extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * "history/rations/classes/per/functional" = classes per one functional requirement
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/^per\/(\w+)$/' => 'deliverable',
    );

    /**
     * Load this metric
     *
     * @return void
     * @throws Metric_History_Ratios_Classes_InvalidDeliverableException
     */
    public function reload()
    {
        $deliverable = $this->_getOption('deliverable');
        if (!$deliverable) {
            $this->value = 0;
            return;
        }
        
        switch ($deliverable) {
            case 'functional':
                $this->value = 0.3;
                break;
            default:
                FaZend_Exception::raise(
                    'Metric_History_Ratios_Classes_InvalidDeliverableException',
                    "Can't find what is '{$deliverable}'"
                );
        }
    }
                 
}
