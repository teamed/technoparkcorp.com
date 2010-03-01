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
 * @version $Id: Interface.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

/**
 * This artifact is in scope and can be baselined
 *
 * @package Artifacts
 * @see theBaselines
 */
interface Model_Artifact_InScope
{
    
    /**
     * Get baseline as collection of text lines
     *
     * @return string[]
     */
    public function getSnapshot();
    
    /**
     * Set baseline to work with for now
     *
     * @param array List of lines to set as baseline
     * @return void
     */
    public function setSnapshot(array $lines);
    
}
