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
            $activity->requestEstimate();
        }
        
        return plural('Requested estimates for activit[ies]', $cnt);
        
    }
    
}
