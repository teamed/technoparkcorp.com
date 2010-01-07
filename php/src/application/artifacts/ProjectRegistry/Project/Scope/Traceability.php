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
class theTraceability extends Model_Artifact_Bag
{
    
    /**
     * Clean all traceability links
     *
     * @return void
     **/
    public function clean()
    {
        // remove all items from the array
        $this->ps()->cleanArray();
    }
    
    /**
     * Add new traceability link
     *
     * @param theTraceabilityLink The element to add
     * @return $this
     **/
    public function add(theTraceabilityLink $link)
    {
        $this[] = $link;
        return $this;
    }
    
    /**
     * Get list of all known source types
     *
     * @return string[]
     **/
    public function getAllSourceTypes() 
    {
        $types = array();
        foreach ($this as $link)
            $types[$link->fromType] = true;
        return array_keys($types);
    }
     
    /**
     * Get list of deliverables, by given type 
     *
     * @param string Type, e.g. "interface" or "method"
     * @return Deliverables_Abstract
     **/
    public function getSourcesByType($type) 
    {
        $deliverables = array();
        foreach ($this as $link) {
            if ($link->fromType == $type)
                $deliverables[] = $this->ps()->parent->deliverables[$link->fromName];
        }
        return $deliverables;
    }
     
    /**
     * Get full list of links by the given source of traceability
     *
     * @param Deliverables_Abstract Source of traceability
     * @return theTraceabilityLink[]
     **/
    public function getLinksBySource(Deliverables_Abstract $source) 
    {
        $links = array();
        foreach ($this as $link) {
            if ($source->name == $link->fromName)
                $links[] = $link;
        }
        return $links;
    }

    /**
     * Calculate average deep
     *
     * @param string|array Name of deliverable or name of class who should be covered
     * @param string|array Name of deliverable or name of class who should cover
     * @return float
     **/
    public function getDeep($froms, $tos)
    {
        // todo
    }
     
    /**
     * Calculate coverage
     *
     * @param string|array Name of deliverable or name of class who should be covered
     * @param string|array Name of deliverable or name of class who should cover
     * @return float
     **/
    public function getCoverage($froms, $tos)
    {
        // todo
    }
     
}
