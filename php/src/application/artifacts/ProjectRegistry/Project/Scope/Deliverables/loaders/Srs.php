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
        foreach ($this->_deliverables->ps()->parent->fzProject()->getAsset(Model_Project::ASSET_SRS)->getEntities() as $entity) {
            $deliverable = theDeliverables::factory($entity->type, $entity->name, $entity->description);
            $entity->deriveDetails($deliverable);
            
            $this->_deliverables->add($deliverable);
        }
    }
    
}
