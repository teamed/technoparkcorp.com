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
 * Total number of defects
 * 
 * @package Artifacts
 */
class Metric_Defects_Total extends Metric_Abstract {

    const DIMENSION_NONE = 0;
    const DIMENSION_SEVERITY = 1;
    const DIMENSION_ARTIFACT = 2;
    const DIMENSION_REPORTER = 3;
    const DIMENSION_ASSIGNEE = 4;
    const DIMENSION_STATUS = 5;

    const STATUS_ANY = 0x00;
    const STATUS_OPEN = 0x01;
    const STATUS_FIXED = 0x02;
    const STATUS_INVALID = 0x04;

    /**
     * Dimension
     *
     * @var integer
     */
    protected $_dimension = self::DIMENSION_NONE;

    /**
     * Status of defect
     *
     * @var integer
     */
    protected $_status = self::STATUS_ANY;

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() {
        $this->value = 180;
        $this->default = 350;
    }
        
}
