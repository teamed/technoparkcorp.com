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
 
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/loaders/Abstract.php';
require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/types/Deliverables/Abstract.php';

/**
 * Load all tickets from defect tracking DB and find traceability in them
 *
 * @package Artifacts
 */
class DeliverablesLoaders_Tickets extends DeliverablesLoaders_Abstract 
{
    
    /**
     * Load all tickets and understand them
     *
     * @return void
     **/
    public function load() 
    {
        $asset = $this->_deliverables->ps()->parent->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS);
            
        foreach ($asset->retrieveBy() as $id) {
            $ticket = $asset->findById($id);
            
            $changes = $ticket->changelog->get('comment')->getChanges();
            // bug($changes);
            
            // we're building a list of deliverables mentioned in this ticket
            $mentioned = array();
            foreach ($changes as $change) {
                if (!preg_match_all(Deliverables_Abstract::REGEX, $change->value, $matches))
                    return;

                foreach ($matches[0] as $match) {
                    if (isset($project->deliverables[$match]))
                        $mentioned[$match] = true;
                }
            }
            $mentioned = array_keys($mentioned);

            // make bi-directional links between them
            for ($i = 0; $i<count($mentioned); $i++) {
                for ($j = $i+1; $j<count($mentioned); $j++) {
                    $links[] = new theTraceabilityLink(
                        $project->deliverables[$mentioned[$i]],
                        $project->deliverables[$mentioned[$j]],
                        0.05,
                        1,
                        'description says: ' . $this->_description
                    );
                    $links[] = new theTraceabilityLink(
                        $project->deliverables[$mentioned[$j]],
                        $project->deliverables[$mentioned[$i]],
                        0.05,
                        1,
                        sprintf('mentioned in #%d: %s', $id, $ticket->changelog->get('summary')->getValue())
                    );
                }
            }
        }
    }
    
}
