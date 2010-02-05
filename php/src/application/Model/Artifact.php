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
 * One simple artifact
 *
 * @package Model
 */
class Model_Artifact extends FaZend_Pos_Abstract implements Model_Artifact_Interface
{
    
    /**
     * When we ping-ed DB last time
     *
     * @var integer
     */
    protected static $_lastPingTime = null;

    /**
     * Get ROOT of the entire storage
     *
     * @return FaZend_Pos_Abstract
     */
    public static function root() 
    {
        return FaZend_Pos_Properties::root();
    }
    
    /**
     * Decorator on top of parent::__get()
     *
     * @param string Name of property to get
     * @return mixed
     */
    public function __get($name) 
    {
        // ping every 10 seconds
        if (self::$_lastPingTime < microtime(true) - 10) {
            // @todo do it somehow else!
            // mysqli_ping(Zend_Db_Table::getDefaultAdapter()->getConnection());
            Zend_Db_Table::getDefaultAdapter()->query('--');
            self::$_lastPingTime = microtime(true);
        }
        return parent::__get($name);
    }

    /**
     * Attach sub-artifact if it's not here already
     *
     * @param string Name of the property to be accessed later
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return $this
     * @param Model_Artifact_PropertyAlreadyExists
     */
    protected function _attach($name, Model_Artifact_Interface $artifact, $property = null) 
    {
        // make sure that we don't save this to DB
        if ($artifact instanceof Model_Artifact_Stateless) {
            $this->ps()->setStatelessProperty($name);
        }

        // don't attach again, if it's already here
        if (!isset($this->$name)) {
            $this->$name = $artifact;
            logg($this->ps()->path . "->{$name} attached");
        } else {
            // We need this in order to get the POS object, and work with it
            // later. Otherwise, we will have $artifact, which is NOT in POS
            // yet, and it will cause problems with POS.
            $artifact = $this->$name;
        }
        
        // initialize the artifact, if necessary, and return $this
        return self::initialize($this, $artifact, $property);
    }
    
    /**
     * Attach sub-artifact if it's not here already, as an item in array
     *
     * @param string|false Key of the array
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return $this
     * @throws Model_Artifact_KeyAlreadyExists
     */
    protected function _attachItem($key, Model_Artifact_Interface $artifact, $property = null) 
    {
        if (!isset($this[$key])) {
            // attach as "new" element to the array or as associative value
            if ($key === false)
                $this[] = $artifact;
            else
                $this[$key] = $artifact;
            logg($this->ps()->path . "[{$key}] attached");
        } else {
            // We need this in order to get the POS object, and work with it
            // later. Otherwise, we will have $artifact, which is NOT in POS
            // yet, and it will cause problems with POS.
            $artifact = $this[$key];
        }
            
        // initialize the artifact, if necessary, and return $this
        return self::initialize($this, $artifact, $property);
    }
    
    /**
     * Initialize child artifact
     *
     * @param Model_Artifact_Interface Root artifact
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return Model_Artifact_Interface Root
     * @throws Model_Artifact_InvalidChildArtifact
     */
    public static function initialize(
        Model_Artifact_Interface $root, 
        Model_Artifact_Interface $artifact, 
        $property) 
    {
        if (!is_null($property) && !($artifact instanceof Model_Artifact)) {
            if (method_exists($artifact, $property))
                $artifact->$property($root);
            else
                $artifact->$property = $root;
        }
        return $root;
    }
    
}
