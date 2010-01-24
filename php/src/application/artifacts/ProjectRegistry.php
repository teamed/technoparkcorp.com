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

/**
 * Collection of projects
 *
 * @package Artifacts
 */
class theProjectRegistry extends Model_Artifact_Bag implements Model_Artifact_Passive 
{

    /**
     * Extra projects to load
     *
     * @var array
     **/
    protected static $_extra = array();
    
    /**
     * The registry is loaded already?
     *
     * @return boolean
     **/
    protected static $_isFresh = false;

    /**
     * Add extra project to the registry
     *
     * The project added by this method will always be in registry,
     * no matter what we get from FaZend.
     *
     * @param string Name of if
     * @param theProject the Project to add
     * @return void
     **/
    public static function addExtra($name, theProject $project) 
    {
        self::$_extra[$name] = $project;
    }

    /**
     * Load all projects
     * 
     * @return void
     */
    public function reload() 
    {
        // remove all items from the array
        $this->ps()->cleanArray();
        
        // get list of projects from FaZend server
        try {
            $fzProjects = Model_Project::retrieveAll();
        } catch (Shared_Project_SoapFailure $e) {
            $fzProjects = array();
            FaZend_Log::err("Failed to retrieve projects: {$e->getMessage()}");
        }
        
        foreach ($fzProjects as $project) {
            // if we DON'T manage this project - skip it
            if (!$project->isManaged())
                continue;
                
            // create new instance and add it to registry
            $this->add($project->name, new theProject());
        }
        
        // add extras
        foreach (self::$_extra as $name=>$project)
            $this->add($name, $project);
            
        self::$_isFresh = true;
    }

    /**
     * Is it loaded with all current projects?
     * 
     * @return boolean
     */
    public function isLoaded() 
    {
        // we should reload it only once per script
        return self::$_isFresh;
    }
    
    /**
     * Add new project to the registry
     *
     * @param string Name of the project to add
     * @param theProject Project to add
     * @return void
     **/
    public function add($name, theProject $project) 
    {
        $this->_attachItem($name, $project);            
        $this[$name]->reload();
    }
    
    /**
     * Return a list of needed people
     *
     * @return theStaffRequest[]
     **/
    public function getStaffRequests() 
    {
        $ini = new Zend_Config_Ini(dirname(__FILE__) . '/ProjectRegistry/wanted.ini', 
            APPLICATION_ENV);
        $requests = new ArrayIterator();
        foreach ($ini as $id=>$person) {
            $request = new theStaffRequest($id);
            
            if (!isset($this[$person->project])) {
                logg("We aren't managing project '{$person->project}', but it exists in wanted.ini");
                continue;
            }
                
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
