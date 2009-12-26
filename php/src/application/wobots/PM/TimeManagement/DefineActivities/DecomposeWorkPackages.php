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

        $this->_project->activityList->reload();

        return count($this->_project->wbs) . ' WPs were decomposed to ' . 
            count($this->_project->activityList->activities) . ' activities';
    }
    
}
