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
 * One artifact, which is a bag -- versions are ignored
 *
 *
 *
 * @package Artifacts
 * @todo Should extend FaZend_POS_Bag
 */
abstract class Model_Artifact_Bag extends Model_Artifact
{

    /**
     * Construct the class
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // we don't need to keep versions in this artifact
        $this->ps()->setIgnoreVersions();
    }

}
