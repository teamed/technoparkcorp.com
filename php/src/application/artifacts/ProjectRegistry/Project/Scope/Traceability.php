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
 * Traceability between deliverables
 *
 * @package Artifacts
 */
class theTraceability extends Model_Artifact_Bag {
    
    /**
     * Clean all traceability links
     *
     * @return void
     **/
    public function clean() {
        // clear all existing traceability links
        foreach ($this as $key=>$metric)
            unset($this[$key]);
    }
    
    /**
     * Add new traceability link
     *
     * @param theTraceabilityLink The element to add
     * @return $this
     **/
    public function add(theTraceabilityLink $link) {
        $this[] = $link;
        return $this;
    }
     
    /**
     * Calculate average deep
     *
     * @param string|array Name of deliverable or name of class who should be covered
     * @param string|array Name of deliverable or name of class who should cover
     * @return float
     **/
    public function getDeep($destinations, $coverers) {
        // todo
    }
     
    /**
     * Calculate coverage
     *
     * @param string|array Name of deliverable or name of class who should be covered
     * @param string|array Name of deliverable or name of class who should cover
     * @return float
     **/
    public function getCoverage($destinations, $coverers) {
        // todo
    }
     
}
