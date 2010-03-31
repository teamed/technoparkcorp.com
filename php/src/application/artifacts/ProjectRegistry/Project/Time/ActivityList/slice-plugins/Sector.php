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

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/slice-plugins/Abstract.php';

/**
 * Sector of the array
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Sector extends Slice_Plugin_Abstract
{

    /**
     * Start index
     *
     * @var integer
     */
    protected $_start = 0;

    /**
     * End index
     *
     * @var integer
     */
    protected $_end = null;

    /**
     * Show only activities, not milestones
     *
     * @param theActivity Activity to check
     * @return boolean
     */
    protected function _isInside(theActivity $activity)
    {
        return ($activity->code >= $this->_start) &&
            (is_null($this->_end) || ($activity->code <= $this->_end));
    }
        
    /**
     * Get a sector of this slice
     *
     * @param integer First element (start with 0)
     * @param integer Last element (NULL means 'till the end')
     * @return Slice_Plugin_Simple
     */
    public function execute($start, $end)
    {
        $this->_start = $start;
        $this->_end = $end;
        return $this;
    }
        
}
