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
 * - glossary
 * and many others, if necessary
 *
 * @package Artifacts
 */
class theDeliverables extends Model_Artifact_Bag implements Model_Artifact_Passive, Model_Artifact_InScope
{
    
    /**
     * Mapping between property name and list of classes to return
     *
     * @var string[]
     */
    protected static $_typeMapping = array(
        'requirements' => array('functional', 'qos', 'interfaces', 'actors', 'glossary', 'useCases'),
        'glossary'     => 'Requirements_Object',
        'actors'       => 'Requirements_Actor',
        'interfaces'   => 'Requirements_Interface',
        'functional'   => 'Requirements_Requirement_Functional',
        'qos'          => 'Requirements_Requirement_Qos',
        'useCases'     => 'Requirements_UseCase',
    
        'design'       => array('packages', 'classes', 'methods', 'files'),
        'classes'      => 'Design_Class',
        'packages'     => 'Design_Package',
        'files'        => 'Design_File',
        'methods'      => 'Design_Method',
    
        'issues'       => 'Defects_Issue',
    );
    
    /**
     * Initialize autoloader, to be called from bootstrap
     *
     * @return void
     * @see bootstrap.php
     */
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

        // reload everything, but all loaders
        DeliverablesLoaders_Abstract::reloadAll($this);

        // discover links, if possible
        foreach ($this as $deliverable) {
            $links = array();
            $deliverable->discoverTraceabilityLinks($this->ps()->parent, $links);
            foreach ($links as $link) {
                $this->traceability->add($link);
            }
        }
        
        // save what was found
        $this->ps()->save();
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
     * Get baseline as collection of text lines (Snapshot)
     *
     * @return string[]
     * @see Model_Artifact_InScope::getSnapshot()
     */
    public function getSnapshot()
    {
        $lines = array();
        foreach ($this as $deliverable) {
            if (!($deliverable instanceof Deliverables_Requirements_Abstract)) {
                continue;
            }
            $lines[] = 
            ($deliverable instanceof Deliverables_Requirements_Requirement ?
                str_repeat('  ', $deliverable->getLevel()) : false).
            $deliverable->name . ': ' . $this->_compress($deliverable->description);
        }
        return $lines;
    }
    
    /**
     * Set baseline to work with for now (Snapshot)
     *
     * @param array List of lines to set as baseline
     * @return void
     * @see Model_Artifact_InScope::setSnapshot()
     */
    public function setSnapshot(array $lines)
    {
        // ignore...
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string|array
     * @throws Deliverables_InvalidShortcutException
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
        
        try {
            require_once dirname(__FILE__) . '/Deliverables/plugins/Abstract.php';
            return Deliverables_Plugin_Abstract::factory($name, $this);
        } catch (Deliverables_Plugin_InvalidPluginException $e) {
            // ignore it
        }
        
        return $this->_getByTypes($name);
    }

    /**
     * Create new deliverable
     *
     * @param string Type of it, which will be added to "Deliverables_"
     * @param string Name of the deliverable, unique!
     * @return Deliverables_Abstract
     **/
    public static function factory($type, $name) 
    {
        $className = 'Deliverables_' . ucfirst($type);
        return new $className($name);        
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
        if (isset($this[strval($deliverable)])) {
            FaZend_Exception::raise(
                'DuplicateDeliverable',
                "Deliverable {$deliverable} is already in the list"
            );
        }
                
        $this[strval($deliverable)] = $deliverable;
        logg("Deliverable attached: $deliverable ({$deliverable->type})");
    }
     
    /**
     * Get entities by type, from $this
     *
     * @param string|array Type or list of types
     * @return ArrayIterator
     * @throws Deliverables_InvalidShortcutException
     */
    protected function _getByTypes($types) 
    {
        $types = self::_getNormalizedTypes($types);
        $list = new ArrayIterator();
        foreach ($this as $deliverable) {
            if (in_array($deliverable->type, $types)) {
                $list[] = $deliverable;
            }
        }
        return $list;
    }
    
    /**
     * Convert raw list of types to the the list of exact types
     *
     * @param string|array Type or list of types, or shortcuts
     * @return string[]
     * @throws Deliverables_InvalidShortcutException
     */
    protected static function _getNormalizedTypes($types) 
    {
        if (!is_array($types)) {
            if (array_key_exists($types, self::$_typeMapping)) {
                $type = self::$_typeMapping[$types];
                if (!is_array($type)) {
                    return array($type);
                }
                return self::_getNormalizedTypes($type);
            }
            FaZend_Exception::raise(
                'Deliverables_InvalidShortcutException', 
                "Shortcut '{$types}' is not found in Deliverables"
            );        
        }
        
        // resolve complex structure
        $result = array();
        foreach ($types as &$type) {
            $result = array_merge($result, self::_getNormalizedTypes($type));
        }
        return $result;
    }
    
    /**
     * Get traceability object from the project
     * 
     * This is necessary for loaders, don't delete the method.
     *
     * @return theTraceability
     */
    protected function _getTraceability() 
    {
        return $this->ps()->parent->traceability;
    }

    /**
     * Compress one line for snapshot
     *
     * @param string
     * @return string
     */
    protected function _compress($line) 
    {
        $replacers = array(
            '/[\t\n\r ]+/' => ' ',
            '/\s+/' => ' ',
            '/[^\w\d,;\s]+/' => '',
        );
        return preg_replace(array_keys($replacers), $replacers, $line);
    }

}
