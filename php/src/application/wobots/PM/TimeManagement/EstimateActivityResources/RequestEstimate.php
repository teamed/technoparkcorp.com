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
 * Request estimate of an activity
 *
 * @package wobots
 */
class RequestEstimate extends Model_Decision_PM
{

    /**
     * Request estimate
     *
     * @return string|false
     * @throws FaZend_Validator_Failure If something happens 
     */
    protected function _make() 
    {
        // validate()
            // ->false($this->_project->objectives->ps()->isApproved(), 'Objectives are not approved yet');

        if (!$this->_project->schedule->isLoaded()) {
            $this->_project->schedule->reload();
        }
        
        $cnt = 0;
        foreach ($this->_project->schedule->activities as $activity) {
            // the activity is too far in the future, there is 
            // no necessity to request estimates now
            if ($activity->start->isEarlier(Zend_Date::now()->add(20, Zend_Date::SECOND)))
                continue;
               
            // if it is already estimated? 
            if ($activity->isCostEstimated() && $activity->isDurationEstimated())
                continue;
            
            // request estimate for this activity
            // $activity->requestEstimate();
        }
        
        // now ignore it
        return false;
        return plural('Requested estimates for activit[ies]', $cnt);
    }
    
}
