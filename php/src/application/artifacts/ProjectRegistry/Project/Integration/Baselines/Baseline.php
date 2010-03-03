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
 * One project baseline
 *
 * @package Artifacts
 * @property string $text Baseline as text, see {@link $this->_getText()}
 * @property integer $length Length of text, see {@link $this->_getLength()}
 */
class theBaseline
{
    
    const CHAPTER_MARKER = '===';
    const TEXT_WIDTH = 80;
    
    /**
     * Textual description of the baseline
     *
     * @var string
     * @see __construct()
     */
    protected $_description;

    /**
     * Collection of arrays of lines
     *
     * Associative array, where keys are names of project artifacts,
     * like "deliverables", "cost", "schedule", etc. And values
     * are arrays of lines to be sent to setSnapshot() method of each
     * baseline.
     *
     * @var array[]
     * @see Model_Artifact_InScope
     * @see __construct()
     */
    protected $_snapshots;
    
    /**
     * Construct the class
     *
     * @param string Description, to be save to {@link $this->_description}
     * @param array[] List of snapshots, to be save to {@link $this->_snapshots}
     * @return void
     * @see $this->_snapshots
     */
    public function __construct($description, array $snapshots)
    {
        $this->_description = $description;
        $this->_snapshots = $snapshots;
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string|array
     * @throws Baseline_InvalidPropertyException
     */
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
            
        $var = '_' . $name;
        if (property_exists($this, $var)) {
            return $this->$var;
        }
        
        FaZend_Exception::raise(
            'Baseline_InvalidPropertyException', 
            "Can't find what is '{$name}' in " . get_class($this)
        );        
    }

    /**
     * Switch project to this baseline
     *
     * @param theProject
     * @return void
     */
    public function switchTo(theProject $project) 
    {
        foreach ($this->_snapshots as $artifact=>$lines) {
            $project->$artifact->setSnapshot($lines);
        }
    }
    
    /**
     * Collect information
     *
     * @param theProject
     * @return array[]
     */
    public static function collect(theProject $project) 
    {
        $lines = array();
        foreach ($project->ps()->properties as $property) {
            // if this is not a property, but an item?
            if (!isset($project->$property)) {
                continue;
            }
                
            // we're interested only in artifacts not in scope
            if (!($project->$property instanceof Model_Artifact_InScope)) {
                continue;
            }
            
            $lines[$property] = $project->$property->getSnapshot();
        }
        return $lines;
    }
    
    /**
     * Convert text back to lines
     *
     * @param string Text received from tickets...
     * @return array[]
     */
    public static function reverse($text) 
    {
        $lines = array();
        // remove line braking symbols
        $text = preg_replace('/\\\\\n\s*/', '', $text);
        
        $marker = preg_quote(self::CHAPTER_MARKER, '/');
        if (preg_match_all("/{$marker}\s?(\w+)\s?\n([^({$marker})]*)/s", $text, $matches)) {
            foreach ($matches[1] as $id=>$artifact) {
                $lines[lcfirst($artifact)] = array_filter(explode("\n", $matches[2][$id]));
            }
        }
        return $lines;
    }
    
    /**
     * Length of baseline text, in char
     *
     * @return void
     */
    protected function _getLength() 
    {
        return strlen($this->text);
    }
    
    /**
     * Get this baseline in text form
     *
     * @return string
     */
    protected function _getText() 
    {
        $lines = array();
        foreach ($this->_snapshots as $artifact=>$lns) {
            $lines = array_merge(
                $lines, 
                array(
                    '', // just empty line before new deliverable section
                    self::CHAPTER_MARKER . ' ' . ucfirst($artifact),
                ),
                $lns
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
    
}
