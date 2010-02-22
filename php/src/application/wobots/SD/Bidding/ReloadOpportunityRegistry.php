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
 * @version $Id: ReloadSupplierRegistry.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 *
 */

/**
 * Reload opportunity registry
 *
 * @package wobots
 */
class ReloadOpportunityRegistry extends Model_Decision
{

    /**
     * Reload it
     *
     * @return string|false
     */
    protected function _make()
    {
        $registry = Model_Artifact::root()->opportunityRegistry;
        
        // maybe it's fresh enough?
        $ageHours = Zend_Date::now()->sub($registry->ps()->updated)->getTimestamp() / (60 * 60);
        if ($ageHours < 24) {
            return sprintf(
                'registry is up to date, %dhrs', 
                $ageHours
            );
        }
        
        logg(
            'There are %d opportunities in the registry now (%dhrs old)', 
            count($registry),
            $ageHours
        );
        
        // reload it
        $registry->reload();
        
        foreach ($registry as $opp) {
            logg(
                'Opportunity %s found',
                $opp->id
            );
        }
        
        logg(
            'There are %d opportunities in the registry, after reloading',
            count($registry)
        );

        return 'Registry reloaded, now it has ' . count($registry) . ' opportunities';
    }
    
}
