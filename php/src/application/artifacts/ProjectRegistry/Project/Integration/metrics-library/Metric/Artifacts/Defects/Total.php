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
 * Total number of defects
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Defects_Total extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/byReporter\/(.*?)/' => 'byReporter',
        '/byComponent\/(.*?)/' => 'byComponent',
        '/byOwner\/(.*?)/' => 'byOwner',
        '/bySeverity\/(.*?)/' => 'bySeverity',
        '/byMilestone\/(.*?)/' => 'byMilestone',
        '/byStatus\/(.*?)/' => 'byStatus',
        );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        foreach ($this->_patterns as $pattern) {
            if (!preg_match('/^by\w+$/', $pattern))
                continue;
            if (is_null($this->_getOption($pattern)))
                continue;

            $method = '_reload' . ucfirst($pattern);
            if (!method_exists($this, $method))
                FaZend_Exception::raise('Metric_Artifact_Defects_Total_InvalidClass',
                    "Method '$method' is not implemented, why?");
                    
            return $this->$method($this->_getOption($pattern));
        }
        
        $tickets = $this->_project->fzProject()->getAsset(Model_Project::ASSET_DEFECTS)->retrieveBy();
        $this->_value = count($tickets);
    }
        
}
