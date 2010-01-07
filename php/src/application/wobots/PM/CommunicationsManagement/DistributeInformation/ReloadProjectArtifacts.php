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
 * Reload project artifact(s)
 *
 * This decision will reload all project artifacts. Maybe in the
 * future we will have to reload just the oldest one, but now we
 * reload them all.
 *
 * @package wobots
 */
class ReloadProjectArtifacts extends Model_Decision_PM
{

    /**
     * Reload the oldest one
     *
     * @return string|false
     */
    protected function _make()
    {
        $reloaded = array();
        foreach ($this->_project->ps()->properties as $property) {
            // if this is not a property, but an item?
            if (!isset($this->_project->$property))
                continue;
                
            // we're interested only in PASSIVE artifacts
            if (!($this->_project->$property instanceof Model_Artifact_Passive))
                continue;
                
            logg("Reloading of [{$this->_project->$property->ps()->path}]...");
            $this->_project->$property->reload();
            logg("Artifact reloaded: $property");
            $reloaded[] = $property;
        }
        
        return "Artifacts reloaded: " . implode(', ', $reloaded);
    }
    
}
