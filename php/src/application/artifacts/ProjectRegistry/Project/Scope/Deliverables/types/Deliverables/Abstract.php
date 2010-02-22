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
 * One element in collection of deliverables
 *
 * @package Artifacts
 */
abstract class Deliverables_Abstract
{
    
    const REGEX = '/System[\.\w]+|(?:[A-Z][a-z]+){2,}|(?:UC|R|QOS)\d+(?:\.\d+)*/';
    
    /**
     * Name of this deliverable
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Collection of attributes
     *
     * @var theDeliverableAttributes
     */
    protected $_attributes;

    /**
     * Construct the class
     *
     * @param string Name of the deliverable
     * @return void
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_attributes = new theDeliverableAttributes();
    }

    /**
     * Convert it to string
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->_name;
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     **/
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
            'PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );        
    }
    
    /**
     * Discover links
     *
     * @param theProject Project to work with
     * @param array List of links
     * @return void
     **/
    public function discoverTraceabilityLinks(theProject $project, array &$links) 
    {
        $description = $this->attributes['description'];
        if (!preg_match_all(self::REGEX, $description, $matches)) {
            return;
        }
            
        foreach ($matches[0] as $match) {
            if (!isset($project->deliverables[$match])) {
                continue;
            }
            
            // don't trace to itself
            if ($this->_name == $match) {
                continue;
            }
                
            $links[] = new theTraceabilityLink(
                $project->deliverables[$this->_name],
                $project->deliverables[$match],
                0.2,
                1,
                'description says: ' . $description
            );
        }
    }
    
    /**
     * Return type of this deliverable
     *
     * @return string
     **/
    protected function _getType()
    {
        return preg_replace('/^Deliverables_/', '', get_class($this));
    }
    
    /**
     * Get description
     *
     * @return string
     */
    protected function _getDescription() 
    {
        return $this->attributes['description']->value;
    }
    
}
