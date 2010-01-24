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
 * One changelog field, priority of the issue
 *
 * @package Model
 */
class Model_Asset_Defects_Issue_Changelog_Field_Priority extends Model_Asset_Defects_Issue_Changelog_Field_Abstract
{

    const MINOR = 1;
    const MAJOR = 2;
    const CRITICAL = 3;
    const BLOCKER = 4;
    
    /**
     * Validate new value
     *
     * @param mixed Value to set
     * @return void
     * @throws Exception if failed
     **/
    protected function _validate($value)
    {
        validate()
            ->type($value, 'integer', "Priority shall be INT only")
            ->true(in_array($value, array(
                self::MINOR,
                self::MAJOR,
                self::CRITICAL,
                self::BLOCKER
            )), "Priority shall be from the predefined list");
            
        return true;
    }

}
