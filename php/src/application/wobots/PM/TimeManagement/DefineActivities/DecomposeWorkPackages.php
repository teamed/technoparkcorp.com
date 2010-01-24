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
 * Decompose work packages
 *
 * @package wobots
 */
class DecomposeWorkPackages extends Model_Decision_PM
{

    /**
     * Get WBS and decompose it into activities
     *
     * @return string|false
     * @throws FaZend_Validator_Failure If something happens 
     */
    protected function _make()
    {
        // validate()
            // ->false($this->_project->objectives->ps()->isApproved(), 'Objectives are not approved yet');

        if (!$this->_project->wbs->isLoaded()) {
            logg("WBS is not loaded yet, we won't do anything here");
            return;
        }
        
        if (!count($this->_project->wbs)) {
            logg("WBS has zero work packages, we can't do anything");
            return;
        }

        logg('There are ' . count($this->_project->wbs) . ' work packages');
        logg('There are ' . count($this->_project->activityList->activities) . 
            ' activities in the Activity List');

        logg('We are reloading the Activity List...');
        $this->_project->activityList->reload();
        logg('The Activity List now has ' . 
            count($this->_project->activityList->activities) . ' activities');

        return count($this->_project->wbs) . ' WPs were decomposed to ' . 
            count($this->_project->activityList->activities) . ' activities';
    }
    
}
