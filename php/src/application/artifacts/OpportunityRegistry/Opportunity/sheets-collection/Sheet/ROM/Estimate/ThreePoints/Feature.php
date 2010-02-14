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
 * @version $Id: ROM.php 655 2010-02-11 08:35:28Z yegor256@yahoo.com $
 *
 */

/**
 * Three-Point estimate feature
 *
 * @package Artifacts
 */
class Sheet_ROM_Estimate_ThreePoints_Feature
{
    
    /**
     * Worst Case
     *
     * @var integer
     */
    protected $_wc;

    /**
     * Best Case
     *
     * @var integer
     */
    protected $_bc;

    /**
     * Most Likely
     *
     * @var integer
     */
    protected $_ml;

    /**
     * Set WorstCase
     *
     * @param integer Value to set
     * @return void
     */
    public function setWc($wc) 
    {
        $this->_wc = $wc;
    }
    
    /**
     * Set BestCase
     *
     * @param integer Value to set
     * @return void
     */
    public function setBc($bc) 
    {
        $this->_bc = $bc;
    }
    
    /**
     * Set MostLikely
     *
     * @param integer Value to set
     * @return void
     */
    public function setMl($ml) 
    {
        $this->_ml = $ml;
    }
    
    /**
     * Get estimate
     *
     * @return integer
     */
    public function getHours() 
    {
        return round(($this->_bc + $this->_wc + $this->_ml * 4) / 6);
    }
    
}
