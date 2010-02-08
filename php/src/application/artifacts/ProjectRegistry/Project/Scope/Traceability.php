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
        // logg("Traceability link added: {$link}");
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
        foreach ($this as $link) {
            $types[$link->fromType] = true;
        }
        return array_keys($types);
    }
     
    /**
     * Get list of all known destination types
     *
     * @return string[]
     **/
    public function getAllDestinationTypes() 
    {
        $types = array();
        foreach ($this as $link) {
            $types[$link->toType] = true;
        }
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
                $deliverables[$link->fromName] = $this->ps()->parent->deliverables[$link->fromName];
        }
        return $deliverables;
    }
     
    /**
     * Get full list of links by the given source of traceability
     *
     * @param string|Deliverables_Abstract Source of traceability
     * @return theTraceabilityLink[]
     **/
    public function getLinksBySource($source) 
    {
        $this->_normalize($source);
        $links = array();
        foreach ($this as $link) {
            if ($link->isFrom($source)) {
                $links[] = $link;
            }
        }
        return $links;
    }

    /**
     * Get full list of links by the given destination
     *
     * @param string|Deliverables_Abstract Destination of traceability
     * @return theTraceabilityLink[]
     **/
    public function getLinksByDestination($dest) 
    {
        $this->_normalize($dest);
        $links = array();
        foreach ($this as $link) {
            if ($link->isTo($dest)) {
                $links[] = $link;
            }
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
     * @param string|array Name of deliverable or name of class who should cover
     * @param string|array Name of deliverable or name of class who should be covered
     * @return float
     **/
    public function getCoverage($from, $to)
    {
        $this->_normalize($from);
        $this->_normalize($to);
        
        // to avoid division by zero
        if (!count($to)) {
            return 0;
        }
        
        $covered = array();
        foreach ($this as $link) {
            if (!$link->isFrom($from) || !$link->isTo($to)) {
                continue;
            }
            $covered[] = strval($link->to);
        }
        return count($covered) / count($to);
    }
    
    /**
     * Normalize the param and make sure it looks like an array of Deliverables
     *
     * @param string|Deliverables_Abstract|array
     * @return void
     * @throws Traceability_UnknownTypeOfArgumentException
     */
    protected function _normalize(&$smth) 
    {
        // make sure it's array in any case
        if (!is_array($smth) && !($smth instanceof ArrayAccess)) {
            $smth = array($smth);
        }
        
        foreach ($smth as $id=>$deliverable) {
            // is is OK already?
            if ($deliverable instanceof Deliverables_Abstract) {
                continue;
            }
            // otherwises it should be a string
            if (!is_string($deliverable)) {
                FaZend_Exception::raise(
                    'Traceability_UnknownTypeOfArgumentException', 
                    "What does this deliverable mean?"
                );        
            }
            try {
                foreach ($this->ps()->parent->deliverables->$deliverable as $found)
                    $smth[] = $found;
                unset($smth[$id]);
                continue;
            } catch (Deliverables_PropertyOrMethodNotFoundException $e) {
                // not found? let'd go next
            }
            
            $deliverable = $this->ps()->parent->deliverables[$deliverable];
        }
    }
     
}
