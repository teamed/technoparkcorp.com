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
class theDeliverables extends Model_Artifact_Bag implements Model_Artifact_Passive
{
    
    /**
     * Initialize autoloader, to be called from bootstrap
     *
     * @return void
     **/
    public static function initAutoloader() 
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Deliverables_');
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/Deliverables/types');

        require_once dirname(__FILE__) . '/Deliverables/loaders/Abstract.php';
    }
    
    /**
     * Load all deliverables
     *
     * @return void
     **/
    public function reload() 
    {
        // clean traceability links
        $this->traceability->clean(); 

        // remove all items from the array
        $this->ps()->cleanArray();

        DeliverablesLoaders_Abstract::reloadAll($this);

        // discover links, if possible
        foreach ($this as $deliverable) {
            $links = array();
            $deliverable->discoverTraceabilityLinks($this->ps()->parent, $links);
            foreach ($links as $link)
                $this->traceability->add($link);
        }
    }
    
    /**
     * Deliverables are loaded?
     *
     * @return boolean
     **/
    public function isLoaded() 
    {
        return (bool)count($this);
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string|array
     * @throws Deliverables_PropertyOrMethodNotFoundException
     **/
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        // deduct trailing 'S' and return by this type
        switch ($name) {
            case 'classes':
                $types = substr($name, 0, -2);
                break;

            case 'glossary':
                $types = 'object';
                break;

            case 'actors':
            case 'interfaces':
            case 'packages':
            case 'files':
            case 'methods':
            case 'useCases':
            case 'issues':
            case 'testCases':
                $types = substr($name, 0, -1);
                break;

            case 'functional':
            case 'qos':
                $types = $name;
                break;
                
            case 'design':
                $types = array('package', 'class', 'method', 'file');
                break;
            
            default:
                FaZend_Exception::raise(
                    'Deliverables_PropertyOrMethodNotFoundException', 
                    "Can't find what is '$name' in " . get_class($this)
                );        
        }
        
        return $this->_getByTypes(self::_convertTypes($types));
    }

    /**
     * Create new deliverable
     *
     * @param string Type of it, which will be added to "Deliverables_"
     * @param string Name of the deliverable, unique!
     * @param string Text description of it
     * @return Deliverables_Abstract
     **/
    public static function factory($type, $name, $description) 
    {
        $types = self::_convertTypes($type);
        $type = array_shift($types);
        $className = 'Deliverables_' . ucfirst($type);
        return new $className($name, $description);        
    }
     
    /**
     * Add new deliverable to the class
     *
     * @param Deliverables_Abstract The element to add
     * @return void
     * @throws DuplicateDeliverable
     **/
    public function add(Deliverables_Abstract $deliverable) 
    {
        // check against double adding
        if (isset($this[strval($deliverable)]))
            FaZend_Exception::raise(
                'DuplicateDeliverable',
                "Deliverable {$deliverable} is already in the list"
            );
                
        $this[strval($deliverable)] = $deliverable;
        logg("Deliverable attached: $deliverable ({$deliverable->type})");
    }
     
    /**
     * Get entities by type
     *
     * @param string|array Type or list of types
     * @return ArrayIterator
     **/
    protected function _getByTypes($types) 
    {
        if (!is_array($types))
            $types = array($types);
        $list = new ArrayIterator();
        foreach ($this as $deliverable) {
            if (in_array($deliverable->type, $types)) {
                $list[] = $deliverable;
            }
        }
        return $list;
    }
    
    /**
     * Convert from text to PHP name suffix
     *
     * @param string|array Type or list of types
     * @return array
     **/
    public static function _convertTypes($types) 
    {
        if (!is_array($types))
            $types = array($types);
        foreach ($types as &$type) {
            switch ($type) {
                case 'functional':
                case 'qos':
                    $type = 'requirement_' . ucfirst($type);
                    break;

                case 'testCase':
                    $type = 'class_' . ucfirst($type);
                    break;
                    
                default:
                    // ... nothing ...
            }
        }
        return $types;
    }
    
    /**
     * Get traceability object from the project
     *
     * @return theTraceability
     **/
    protected function _getTraceability() 
    {
        return $this->ps()->parent->traceability;
    }

}
