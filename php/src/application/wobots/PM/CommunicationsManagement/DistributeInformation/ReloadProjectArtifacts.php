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
            
            // maybe it's fresh enough?    
            if ($this->_project->$property instanceof Model_Artifact) {
                $ageHours = Zend_Date::now()
                    ->sub($this->_project->$property->ps()->updated)
                    ->getTimestamp() / (60 * 60);
                if ($ageHours < 24) {
                    logg(
                        '%s is up to date, %dhrs', 
                        $property,
                        $ageHours
                    );
                    continue;
                }
            }
            
            // we reload it explicitly, no matter whether it's loaded or not
            logg("Reloading of [{$this->_project->$property->ps()->path}]...");
            $this->_project->$property->reload();
            logg("Artifact reloaded: $property");
            $reloaded[] = $property;
        }
        
        if (!count($reloaded))
            return 'Nothing reloaded';
        
        return "Artifacts reloaded: " . implode(', ', $reloaded);
    }
    
}
