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

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * One requirement, abstract class
 *
 * @package Artifacts
 */
abstract class Deliverables_Requirements_Requirement extends Deliverables_Abstract
{
        
    /**
     * Level of requirement (0...)
     *
     * For R1.3.7 it will return 2. For R4 it will return 0.
     *
     * @return integer
     */
    public function getLevel() 
    {
        return substr_count($this->_name, '.');
    }
       
    /**
     * Discover links
     *
     * @param theProject Project to work with
     * @param array List of links
     * @return void
     **/
    public function discoverTraceabilityLinks(theProject $project, array &$links) 
    {
        parent::discoverTraceabilityLinks($project, $links);
        
        // except the last one
        $sectors = array_slice(explode('.', $this->_name), 0, -1);
        
        foreach ($sectors as $id=>$sector) {
            $parent = implode('.', array_slice($sectors, 0, $id+1));
            
            if (!isset($project->deliverables[$parent]))
                continue;
                
            $links[] = new theTraceabilityLink(
                $project->deliverables[$this->_name],
                $project->deliverables[$parent],
                0.5,
                1,
                'it is parent of ' . $this->_name
            );
        }
    }
    
}
