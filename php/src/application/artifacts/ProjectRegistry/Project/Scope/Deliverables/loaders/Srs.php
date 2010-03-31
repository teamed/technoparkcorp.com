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
     * Collection of XPATH-s and class names
     *
     * @var string
     * @see load()
     */
    protected static $_xpaths = array(
        '//object[@type!="actor" or not(@type)]' => 'Requirements_Object',
        '//object[@type="actor"]' => 'Requirements_Actor',
        '//fur' => 'Requirements_Requirement_Functional',
        '//qos' => 'Requirements_Requirement_Qos',
        '//interface' => 'Requirements_Interface',
        '//usecase' => 'Requirements_UseCase',
    );
    
    /**
     * Load all deliverables
     *
     * @return void
     */
    public function load() 
    {
        logg('SRS loading started...');
        $xml = $this->_deliverables->ps()->parent->fzProject()
            ->getAsset(Model_Project::ASSET_SRS)->rqdqlQuery('');

        $found = array();
        foreach (self::$_xpaths as $xpath=>$className) {
            foreach ($xml->xpath($xpath) as $entity) {
                $deliverable = theDeliverables::factory(
                    $className,
                    strval($entity->attributes()->id)
                );
                $found[] = $deliverable->name;
            
                $deliverable->attributes['description']->add(
                    strval($entity->description)
                );

                foreach ($entity->xpath('//attr') as $attrib) {
                    $id = strval($attrib->attributes()->id);
                    switch (true) {
                        case ($id == 'out'):
                            $deliverable->attributes['out']->add(true);
                            break;

                        case ($id == 'must'):
                            $deliverable->attributes['priority']->add(9);
                            break;

                        case preg_match('/^p(\d+)$/i', $id, $matches):
                            $deliverable->attributes['priority']->add(intval($matches[1]));
                            break;

                        case preg_match('/^c(\d+)$/i', $id, $matches):
                            $deliverable->attributes['complexity']->add(intval($matches[1]));
                            break;

                        default:
                            // ignore it...
                    }
                }

                $this->_deliverables->add($deliverable);
            }
        }
        
        // update priorities of all functional requirements
        $this->_updatePriorities();
        
        logg(
            'SRS loading finished, %d deliverables found: %s',
            count($found),
            implode(', ', $found)
        );
    }
    
    /**
     * Update priorities of all functional requirements
     *
     * @return void
     */
    protected function _updatePriorities() 
    {
        foreach ($this->_deliverables->functional as $req) {
            $parent = $req;
            $priority = 1;
            // try to set it to parent priority
            while (true) {
                if (!$parent->getLevel()) {
                    break;
                }
                if ($parent->priority) {
                    $priority = max($parent->priority->value, $priority);
                    break;
                }
                if (isset($this->_deliverables[$parent->parentName])) {
                    $parent = $this->_deliverables[$parent->parentName];
                } else {
                    // yes, it's possible. for example R4.3 is defined, but R4 is missed.
                    // in such a case we just ignore this item.
                    break;
                }
            }
            
            $req->attributes['priority']->add($priority);
        }
    }
    
}
