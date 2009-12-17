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
 * This is temporary structure required while POS is in development
 *
 * You just set USE_POS to false and you won't have any persistence of objects 
 */
defined('USE_POS') or define('USE_POS', false);

if (USE_POS) {
    class tempArtifact extends FaZend_POS_Array {
    }
} else {
    class tempArtifact extends ArrayIterator {

        /**
         * To be implemented in FaZend_POS_Abstract
         *
         * @var object
         */
        private $__ps;

        /**
         * To be implemented in FaZend_POS_Abstract
         *
         * @return object
         */
        public function ps() {
            if (!isset($this->__ps))
                $this->__ps = new FaZend_StdObject();
            return $this->__ps;
        }

        /**
         * Construct the class
         *
         * @return void
         * @todo remove it, it should be in POS
         */
        public function __construct() {
            parent::__construct();
            $this->_init();
        }

        /**
         * Override it
         *
         * @return void
         * @todo remove it, it should be in POS
         **/
        protected function _init() {
        }

    }
}

/**
 * One simple artifact
 *
 * @package Model
 */
class Model_Artifact extends tempArtifact implements Model_Artifact_Interface 
{

    /**
     * Root of all artifacts
     *
     * @var Model_Artifact
     */
    protected static $_root = null;

    /**
     * Root of the entire hierarchy
     *
     * @return Model_Artifact
     */
    public static function root() 
    {
        if (!is_null(self::$_root))
            return self::$_root;
            
        if (USE_POS)
            $root = FaZend_POS::root();
        else
            $root = new Model_Artifact();
        
        foreach (array(
            'projectRegistry' => new theProjectRegistry(),
            'supplierRegistry' => new theSupplierRegistry(),
            'statements' => new theStatements(),
            ) as $name=>$artifact) {
            // we need this validation for POS
            if (!isset($root->name)) {
                // add them to the root, one by one
                $root->$name = $artifact;
                self::_initialize($root, $artifact, null);
            }
        }
        return self::$_root = $root;
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
        if (!USE_POS && isset($this->$name)) {
            FaZend_Exception::raise('Model_Artifact_PropertyAlreadyExists',
                "Can't attach '{$name}' again to " . get_class($this));
        }
        $this->$name = $artifact;
        
        // initialize the artifact, if necessary
        self::_initialize($this, $artifact, $property);
        return $this;
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
        if (!USE_POS && isset($this[$key])) {
            $keys = array();
            foreach ($this as $k=>$val)
                $keys[] = $k;
            FaZend_Exception::raise('Model_Artifact_KeyAlreadyExists',
                "Can't attach item '{$key}' again to " . get_class($this) . 
                '[' . implode(', ', $keys) . ']');
        }

        // attach as "new" element to the array or as associative value
        if ($key === false)
            $this[] = $artifact;
        else
            $this[$key] = $artifact;
            
        // initialize the artifact, if necessary
        self::_initialize($this, $artifact, $property);
        return $this;
    }
    
    /**
     * Initialize child artifact
     *
     * @param mixed Root object
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return void
     * @throws Model_Artifact_InvalidChildArtifact
     */
    protected static function _initialize($root, Model_Artifact_Interface $artifact, $property) 
    {
        if (!USE_POS && is_null($property) && !($artifact instanceof Model_Artifact_Stateless)) {
            if (method_exists($artifact, 'ps')) {
                $artifact->ps()->parent = $root; // TODO: this should be removed and implemented in FaZend
            }
        } elseif (!is_null($property) && ($artifact instanceof Model_Artifact_Stateless)) {
            if (method_exists($artifact, $property))
                $artifact->$property($root);
            else
                $artifact->$property = $root;
        } elseif (!is_null($property)) {
            FaZend_Exception::raise('Model_Artifact_InvalidChildArtifact', 
                'Artifact ' . get_class($artifact) . ' is not stateless');
        }
            
        // reload it if it's empty now and requires loading
        if (($artifact instanceof Model_Artifact_Passive) && !$artifact->isLoaded())
            $artifact->reload();
    }
    
}
