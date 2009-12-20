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
 * Collection of projects
 *
 * @package Artifacts
 */
class theProjectRegistry extends Model_Artifact implements Model_Artifact_Passive 
{

    /**
     * Load all projects
     * 
     * @return void
     */
    public function reload() 
    {
        foreach (Model_Project::retrieveAll() as $project) {
            // if we DON'T manage this project - skip it
            if (!$project->isManaged())
                continue;
                
            // create new instance and add it to registry
            $p = new theProject();
            $p->name = $project->name;
            $this->add($p);
        }
    }

    /**
     * Is it loaded with all current projects?
     * 
     * @return boolean
     */
    public function isLoaded() 
    {
        return count($this) > 0;
    }
    
    /**
     * Add new project to the registry
     *
     * @param theProject Project to add
     * @return void
     **/
    public function add(theProject $project) 
    {
        $this->_attachItem($project->name, $project);            
    }
    
    /**
     * Return a list of needed people
     *
     * @return theStaffRequest[]
     **/
    public function getStaffRequests() 
    {
        $ini = new Zend_Config_Ini(dirname(__FILE__) . '/ProjectRegistry/wanted.ini', APPLICATION_ENV);
        $requests = new ArrayIterator();
        foreach ($ini as $id=>$person) {
            $request = new theStaffRequest($id);
            
            $project = $this[$person->project];
            $request->setProject($project);
            $request->setRole($project->staffAssignments->createRole($person->role));
            
            foreach ($person->skills as $skill=>$grade)
                $request->addSkill($skill, $grade);
            
            $requests[$id] = $request;
        }
        return $requests;
    }
    
    /**
     * Return a unique staff request
     *
     * @param string ID of the request to return
     * @return theStaffRequest
     **/
    public function getStaffRequestById($id) 
    {
        $requests = $this->getStaffRequests();
        return $requests[$id];
    }
    
}
