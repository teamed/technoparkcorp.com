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
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Deliverables_');
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/Deliverables/types');

        // clean traceability links
        $this->traceability->clean(); 

        // clean all existing deliverables
        foreach ($this as $key=>$metric)
            unset($this[$key]);

        // execute ALL loaders one after another
        require_once dirname(__FILE__) . '/Deliverables/loaders/Abstract.php';
        $loaders = DeliverablesLoaders_Abstract::retrieveAll($this);
        foreach ($loaders as $loader)
            $loader->load();
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
        
        // deduct trailing 'S' and return by this type
        switch ($name) {
            case 'classes':
                $type = substr($name, 0, -2);
                break;

            case 'actors':
            case 'interfaces':
            case 'packages':
            case 'files':
            case 'useCases':
            case 'issues':
            case 'testCases':
                $type = substr($name, 0, -1);
                break;

            case 'functional':
            case 'qos':
                $type = $name;
                break;
            
            default:
                FaZend_Exception::raise('Model_Wiki_PropertyOrMethodNotFound', 
                    "Can't find what is '$name' in " . get_class($this));        
        }
        
        return $this->_getByType(self::_convertType($type));
    }

    /**
     * Create new deliverable
     *
     * @param string Type of it
     * @param string Name of the deliverable
     * @param string Text description of it
     * @return Deliverables_Abstract
     **/
    public static function factory($type, $name, $description) {
        $className = 'Deliverables_' . ucfirst(self::_convertType($type));
        return new $className($name, $description);        
    }
     
    /**
     * Add new deliverable to the class
     *
     * @param Deliverables_Abstract The element to add
     * @return void
     **/
    public function add(Deliverables_Abstract $deliverable) {
        $this[] = $deliverable;
    }
     
    /**
     * Get entities by type
     *
     * @param string Type
     * @return ArrayIterator
     **/
    protected function _getByType($type) {
        $list = new ArrayIterator();
        foreach ($this as $deliverable) {
            if ($deliverable->type == $type)
                $list[] = $deliverable;
        }
        return $list;
    }
    
    /**
     * Convert from text to PHP name suffix
     *
     * @return string
     **/
    public static function _convertType($type) {
        switch ($type) {
            case 'functional':
            case 'qos':
                return 'requirement_' . ucfirst($type);

            case 'testCase':
                return 'class_' . ucfirst($type);
        }
        
        return $type;
    }
    
    /**
     * Get traceability object from the project
     *
     * @return theTraceability
     **/
    protected function _getTraceability() {
        return $this->ps()->parent->traceability;
    }

}
