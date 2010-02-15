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
 * Three-Point estimate
 *
 * Format to use:
 * <code>
 * F1: BC=888, WC=999, ML=666
 * </code>
 *
 * @package Artifacts
 */
class Sheet_ROM_Estimate_ThreePoints extends Sheet_ROM_Estimate_Abstract
{
    
    /**
     * List of features and their estimates
     *
     * <code>
     * array(
     *   'F1 => Sheet_ROM_Estimate_ThreePoints_Feature(),
     *   'F2' => Sheet_ROM_Estimate_ThreePoints_Feature(),
     * );
     * </code>
     *
     * @var array
     * @see _init()
     */
    protected $_features;
    
    /**
     * Parse lines
     *
     * @return void
     */
    protected function _init() 
    {
        $this->_features = array();
        foreach ($this->_lines as $line) {
            $line = trim($line, "\t\r\n ");
            if (!preg_match('/^(f\d+):((?:\s*,?\s*(?:wc|ml|bc)\s*=\s*\d+){3})$/i', $line, $matches)) {
                continue;
            }
            
            $feature = new Sheet_ROM_Estimate_ThreePoints_Feature($matches[1]);
            
            $exp = explode(',', $matches[2]);
            foreach ($exp as $sector) {
                list($param, $value) = explode('=', $sector);
                $method = 'set' . ucfirst(strtolower(trim($param)));
                $feature->$method(intval($value));
            }
            
            $this->_features[] = $feature;
        }
    }
    
    /**
     * Get total amount of hours, the estimate
     *
     * @return integer
     */
    protected function _getHours()
    {
        return round((($this->bc + $this->wc + 4 * $this->ml) / 6) * $this->multiplier);
    }
    
    /**
     * Multiplier to conver coding time into project time
     *
     * @return float
     */
    protected function _getMultiplier() 
    {
        return 3.5;
    }
    
    /**
     * Get worst case
     *
     * @return integer
     */
    protected function _getWc() 
    {
        return $this->_getEstimateSummary('getWc');
    }
    
    /**
     * Get best case
     *
     * @return integer
     */
    protected function _getBc() 
    {
        return $this->_getEstimateSummary('getBc');
    }
    
    /**
     * Get most likely
     *
     * @return integer
     */
    protected function _getMl() 
    {
        return $this->_getEstimateSummary('getMl');
    }
    
    /**
     * Get total
     *
     * @param string Name of field in the estimate
     * @return integer
     */
    protected function _getEstimateSummary($method) 
    {
        $hours = 0;
        foreach ($this->_features as $feature) {
            $hours += $feature->$method();
        }
        return $hours;
    }
    
}
