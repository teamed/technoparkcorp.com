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

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/activity-plugins/Abstract.php';

/**
 * Request estimate
 * 
 * @package Activity_Plugin
 */
class Activity_Plugin_RequestEstimate extends Activity_Plugin_Abstract
{

    /**
     * Execute it
     *
     * @return boolean
     **/
    public function execute() 
    {
        if ($this->_activity->isMilestone())
            return;
        
        // make sure it exists
        $this->_activity->makeAlive();

        // if it is already estimated - skip
        if ($this->_activity->isCostEstimated() && $this->_activity->isDurationEstimated())
            return;
        
        // ask performer to estimate it - first ask (and never ask again)
        if ($this->_issue->askOnce('requestEstimate', 'Could you please estimate this activity?', null))
            return;
    }
                            
}
