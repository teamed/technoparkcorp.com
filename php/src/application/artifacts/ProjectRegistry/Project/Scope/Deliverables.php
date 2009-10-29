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
 * All deliverables in one big holder
 *
 * They include:
 * - actors
 * - interfaces
 * - use cases
 * - classes
 * - files
 * - packages
 * - test cases
 * - methods
 * and many others, if necessary
 *
 * @package Artifacts
 */
class theDeliverables extends Model_Artifact_Bag implements Model_Artifact_Passive {
    
    /**
     * Load all deliverables
     *
     * @return void
     **/
    public function reload() {
        // clear all existing deliverables
        foreach ($this as $key=>$metric)
            unset($this[$key]);

        foreach ($this->ps()->parent->fzProject()->getWiki()->retrieveAll() as $entity)
            $this[$entity->name] = $entity;
    }
    
    /**
     * Deliverables are loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));        
    }

    /**
     * Get list of actors
     *
     * @return Model_Wiki_Entity_Abstract[]
     **/
    protected function _getActors() {
        return $this->_getByRegex('/^Actor[A-Z][\w\d]+$/');
    }
    
    /**
     * Get list of interfaces
     *
     * @return Model_Wiki_Entity_Abstract[]
     **/
    protected function _getInterfaces() {
        return $this->_getByRegex('/^If[A-Z][\w\d]+$/');
    }
    
    /**
     * Get list of functional requirements
     *
     * @return Model_Wiki_Entity_Abstract[]
     **/
    protected function _getFunctional() {
        return $this->_getByRegex('/^R[\d]+(\.\d+)*$/');
    }
    
    /**
     * Get list of quality of service requirments
     *
     * @return Model_Wiki_Entity_Abstract[]
     **/
    protected function _getQos() {
        return $this->_getByRegex('/^QOS[\d]+(\.\d+)*$/');
    }
    
    /**
     * Get entities by regexp
     *
     * Returns a list of entities, which names match given regexp
     *
     * @param string Prefix
     * @return array
     **/
    protected function _getByType($regex) {
        $list = new ArrayIterator();
        foreach ($this as $key=>$entity) {
            if (preg_match($regex, $key))
                $list[$key] = $entity;
        }
        return $list;
    }

}
