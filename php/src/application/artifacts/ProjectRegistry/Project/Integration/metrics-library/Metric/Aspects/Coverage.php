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
 * Requirements compliance to our writing convention
 * 
 * @see http://fazend.com/a/2009-12-WikiNotation.html
 * @package Artifacts
 */
class Metric_Aspects_Coverage extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     */
    protected $_patterns = array(
        '/([a-z]+)\/([a-z]\w+)/' => 'destination, source',
    );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() 
    {
        // general coverage? something unknown
        if (!$this->_getOption('destination'))
            return $this->value = 0;
    
        // calculate coverage
        $this->value = $this->_project->traceability->getCoverage(
            $this->_getOption('source'),
            $this->_getOption('destination')
        );
    }
                
}
