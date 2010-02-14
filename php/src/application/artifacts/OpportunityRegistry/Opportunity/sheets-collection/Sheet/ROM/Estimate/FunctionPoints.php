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
 * Function Point (FP) estimate
 *
 * Format to use:
 * <code>
 * F1: FP=10, C=8
 * </code>
 *
 * @package Artifacts
 */
class Sheet_ROM_Estimate_FunctionPoints extends Sheet_ROM_Estimate_Abstract
{
    
    /**
     * Function points total
     *
     * @var integer
     */
    protected $_fp;
    
    /**
     * Parse lines
     *
     * @return void
     */
    protected function _init() 
    {
        $this->_fp = 0;
        foreach ($this->_lines as $line) {
            $line = trim($line, "\t\r\n ");
            if (!preg_match('/^(f\d+):((?:\s*,?\s*(?:fp|c)\s*=\s*\d+){2})$/i', $line, $matches)) {
                continue;
            }
            
            $fp = 0;
            $complexity = 0;
            $exp = explode(',', strtolower($matches[2]));
            foreach ($exp as $sector) {
                list($param, $value) = explode('=', $sector);
                switch (trim($param)) {
                    case 'fp':
                        $fp = $value;
                        break;
                    case 'c':
                        $complexity = $value;
                        break;
                    default:
                        // ...?
                }
            }
            
            $this->_fp += $fp * $complexity;
        }
    }
    
    /**
     * Get total amount of hours, the estimate
     *
     * @return integer
     */
    protected function _getHours()
    {
        return $this->_fp;
    }
    
}
