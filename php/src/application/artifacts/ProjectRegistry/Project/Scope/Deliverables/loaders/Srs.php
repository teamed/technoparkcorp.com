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
 * Load all elements from SRS
 *
 * @package Artifacts
 */
class DeliverablesLoaders_Srs extends DeliverablesLoaders_Abstract
{
    
    /**
     * Load all deliverables
     *
     * @return void
     **/
    public function load() 
    {
        logg('SRS loading started...');
        $entities = $this->_deliverables->ps()->parent->fzProject()
            ->getAsset(Model_Project::ASSET_SRS)->getEntities();

        foreach ($entities as $entity) {
            $type = ucfirst($entity->type);
            switch ($type) {
                case 'Functional':
                case 'Qos':
                    $type = 'Requirements_Requirement_' . $type;
                    break;
                default:
                    $type = 'Requirements_' . $type;
                    break;
            }
            
            $deliverable = theDeliverables::factory($type, $entity->name);
            $deliverable->attributes['description']->add($entity->description);

            foreach (array_filter($entity->attributes) as $attrib=>$value) {
                switch (true) {
                    case $attrib == 'out':
                        $deliverable->attributes['out']->add(true);
                        break;

                    case $attrib == 'must':
                        $deliverable->attributes['priority']->add(5);
                        break;

                    case preg_match('/^p(\d+)$/i', $attrib, $matches):
                        $deliverable->attributes['priority']->add(intval($matches[1]));
                        break;

                    case preg_match('/^c(\d+)$/i', $attrib, $matches):
                        $deliverable->attributes['complexity']->add(intval($matches[1]));
                        break;

                    default:
                        // ignore it...
                }
            }

            $this->_deliverables->add($deliverable);
        }
        logg('SRS loading finished, %d deliverables found', count($entities));
    }
    
}
