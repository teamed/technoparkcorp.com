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
 * @version $Id: Objectives.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

/**
 * Project baselines manager
 *
 * @package Artifacts
 */
class theBaselines extends Model_Artifact_Bag implements Model_Artifact_Passive
{
    
    const CHAPTER_MARKER = '===';
    const TEXT_WIDTH = 80;
    
    /**
     * Reload information about baselines
     *
     * @return void
     */
    public function reload() 
    {
        //...
    }
    
    /**
     * Is it loaded (TRUE) or requires reloading (FALSE)
     *
     * @return boolean
     */
    public function isLoaded() 
    {
        return false;
    }
    
    /**
     * Switch all artifacts to the selected baseline
     *
     * @param string Name of the snapshot
     * @return void
     * @throws Baselines_InvalidBaselineNameException
     */
    public function switchTo($name) 
    {
        if (!isset($this[$name])) {
            FaZend_Exception::raise(
                'Baselines_InvalidBaselineNameException',
                "Baseline not found in collection: '{$name}'"
            );
        }
        $this[$name]->switchTo($this->ps()->parent);
    }
    
    /**
     * Get project snapshot
     *
     * @return string
     */
    public function getSnapshot() 
    {
        $lines = array();
        $project = $this->ps()->parent;
        foreach ($project->ps()->properties as $property) {
            // if this is not a property, but an item?
            if (!isset($project->$property)) {
                continue;
            }
                
            // we're interested only in artifacts not in scope
            if (!($project->$property instanceof Model_Artifact_InScope)) {
                continue;
            }
            
            $lines = array_merge(
                $lines, 
                array(
                    '', // just empty line before new deliverable section
                    self::CHAPTER_MARKER . ' ' . ucfirst($property),
                ),
                $project->$property->getSnapshot()
            );
        }
        
        // break long lines onto shorter
        foreach ($lines as &$line) {
            if (preg_match('/^(\s+)/', $line, $matches)) {
                $prefix = $matches[1];
            } else {
                $prefix = '';
            }
            $line = wordwrap($line, self::TEXT_WIDTH, " \\\n" . $prefix);
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * Add new snapshot to the collection, and create baseline
     *
     * @param string Name of snapshot
     * @param string Description of snapshot
     * @param string Text received from tickets...
     * @return theBaseline
     */
    public function addSnapshot($name, $description, $text) 
    {
        validate()
            ->alnum($name, "Only letters and numbers, '{$name}' is not valid")
            ->true(strlen($name) > 0, 'Empty names are prohibited');
            
        $snapshots = array();
        
        // remove line braking symbols
        $text = preg_replace('/\\\\\n\s*/', '', $text);
        
        $marker = preg_quote(self::CHAPTER_MARKER, '/');
        if (preg_match_all("/{$marker}\s?(\w+)\s?\n([^({$marker})]*)/s", $text, $matches)) {
            foreach ($matches[1] as $id=>$artifact) {
                $lines = array_filter(explode("\n", $matches[2][$id]));
                foreach ($lines as &$line) {
                    $line = trim($line);
                }
                $snapshots[lcfirst($artifact)] = $lines;
            }
        }
        return $this[$name] = new theBaseline($description, $snapshots);
    }
    
}
