<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One activity
 *
 * @package Artifacts
 */
class theActivity extends Model_Artifact_Stateless {

    /**
     * Work package it came from
     *
     * @var theWorkPackage
     */
    public $wp;

    /**
     * Is it already assigned?
     *
     * @return boolean
     */
    public function isAssigned() {
    }

    /**
     * Is cost already estimated?
     *
     * @return boolean
     */
    public function isCostEstimated() {
    }

    /**
     * Is cost estimate already requested?
     *
     * @return boolean
     * @todo implement it
     */
    public function isCostEstimateRequested() {
    }

    /**
     * Request cost estimate from the performer
     *
     * @return void
     * @todo implement it
     */
    public function requestCostEstimate() {
    }

}
