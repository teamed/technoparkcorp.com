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
 * One milestone
 *
 * @package Artifacts
 */
class theMilestone extends theActivity
{

    /**
     * Factory method
     *
     * @param theActivities Holder of this activity
     * @param theWorkPackage Originator of the activity
     * @param string Unique code for this work package
     * @return theMilestone
     **/
    public static function factoryMilestone(theActivities $activities, $wp, $code)
    {
        return new theMilestone($activities, $wp, $code);
    }

}
