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

require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/loaders/Abstract.php';

/**
 * Load all design elements from code
 *
 * @package Artifacts
 */
class DeliverablesLoaders_Design extends DeliverablesLoaders_Abstract
{
    
    /**
     * Load all deliverables
     *
     * @return void
     **/
    public function load() 
    {
        logg('Design loading started...');
        $project = $this->_deliverables->ps()->parent;

        $components = $project->fzProject()
            ->getAsset(Model_Project::ASSET_DESIGN)->getComponents();

        logg('Found %d components', count($components));
        foreach ($components as $component) {
            $deliverable = theDeliverables::factory(
                'Design_' . ucfirst($component->type), 
                $component->name
            );
            
            // save here a list of tickets waiting for implementation
            $deliverable->setTodoTickets($component->todoTickets);
            
            if (isset($project->deliverables[$deliverable->name])) {
                logg('Duplicate deliverable: %s (%s)', $deliverable->name, $deliverable->type);
                continue;
            }
            
            // add description
            $deliverable->attributes['description']->add($component->description);
            
            $project->deliverables->add($deliverable);
            
            foreach ($component->traces as $trace) {
                if (!isset($project->deliverables[$trace])) {
                    continue;
                }
                try {
                    $project->traceability->add(
                        new theTraceabilityLink(
                            $deliverable,
                            $project->deliverables[$trace],
                            0.75,
                            1,
                            "@see {$trace}"
                        )
                    );
                } catch (TraceabilityLink_ReversedLinkException $e) {
                    // ignore it...
                }
            }
        }
        logg('Design loading finished, %d components loaded', count($components));
    }
    
}
