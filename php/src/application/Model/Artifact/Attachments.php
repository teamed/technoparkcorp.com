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
 * One artifact, which is a collection of external files
 *
 * @package Artifacts
 */
class Model_Artifact_Attachments extends ArrayIterator implements Model_Artifact_Stateless
{

    /**
     * Location of attachments
     *
     * @var string
     */
    protected static $_location = null;

    /**
     * Get location of all attachments
     *
     * @return string Absolute directory name
     **/
    public static function getLocation()
    {
        if (self::$_location === null)
            self::$_location = APPLICATION_PATH . '/../../attachments';
        return self::$_location;
    }
    
    /**
     * Set location of all attachments
     *
     * @param string New location
     * @return string Absolute directory name
     **/
    public static function setLocation($location)
    {
        self::$_location = $location;
    }
    
}
