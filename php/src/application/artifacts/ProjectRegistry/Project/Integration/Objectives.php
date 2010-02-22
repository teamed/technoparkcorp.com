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
 * Project objectives
 *
 * @package Artifacts
 */
class theObjectives extends Model_Artifact
{
    
    /**
     * Set one objective
     *
     * @param string Name of the objective
     * @param integer|null Value of the objective or NULL if you want to remove it
     * @return void
     **/
    public function setObjective($name, $value) 
    {
        if (is_null($value)) {
            if (isset($this[$name])) {
                unset($this[$name]);
                logg('Objective %s removed', $name);
            } else {
                logg('Objective %s is absent, nothing to remove', $name);
            }
        } else {
            validate()
                ->true(is_numeric($value), "Value should be numeric only");
        
            if (!isset($this[$name]) || !($this[$name] instanceof theObjective)) {
                $this[$name] = new theObjective($value);
            } else {
                $this[$name]->setValue($value);
            }
            logg('Objective %s is set to %d', $name, $value);
        }
        $this->ps()->setDirty();
    }
        
}
