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

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/loaders/Abstract.php';
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * Load all issues from defect tracking DB and find traceability in them
 *
 * @package Artifacts
 */
class DeliverablesLoaders_Issues extends DeliverablesLoaders_Abstract
{
    
    /**
     * Load all tickets and understand them
     *
     * @return void
     **/
    public function load() 
    {
        $this->_loadFirst('srs');
        
        logg('Issues loading started...');
        $project = $this->_deliverables->ps()->parent;
            
        foreach ($project->issues as $issue) {
            // add it to the list of deliverables
            $issueName = '#' .  $issue->id;
            $project->deliverables->add(
                theDeliverables::factory(
                    'Defects_Issue', 
                    $issueName, 
                    $issue->changelog->get('summary')->getValue()
                )
            );
            
            $changes = $issue->changelog->get('comment')->getChanges();
            
            // we're building a list of deliverables mentioned in this ticket
            $mentioned = array();
            foreach ($changes as $change) {
                if (!preg_match_all(Deliverables_Abstract::REGEX, $change->value, $matches)) {
                    continue;
                }

                foreach ($matches[0] as $match) {
                    if (isset($project->deliverables[$match])) {
                        $mentioned[$match] = true;
                    }
                }
            }
            $mentioned = array_keys($mentioned);

            // make bi-directional links between them
            foreach ($mentioned as $name) {
                $project->traceability->add(
                    new theTraceabilityLink(
                        $project->deliverables[$issueName],
                        $project->deliverables[$name],
                        0.05,
                        1,
                        "mentioned in {$issueName}: " . $issue->changelog->get('summary')->getValue()
                    )
                );
            }
        }
        logg('Issues loading finished, %d tickets processed', count($project->issues));
    }
    
}
