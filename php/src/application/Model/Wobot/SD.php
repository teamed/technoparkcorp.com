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
 * Sales Director
 *
 * @package Model
 */
class Model_Wobot_SD extends Model_Wobot
{

    /**
     * Returns a list of all possible wobot names of this given type/class
     *
     * @return string[]
     */
    public static function getAllNames() 
    {
        return array('SD');
    }

    /**
     * Get the full name of the human-wobot
     *
     * @return string
     */
    public function getHumanName() 
    {
        return 'Yegor Bugayenko';
    }

}
