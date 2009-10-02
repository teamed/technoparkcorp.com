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
 * One simple artifact
 *
 * @package Model
 */
class Model_Artifact extends ArrayIterator 
    implements Model_Artifact_Interface {

    /**
     * root
     */
    protected static $_root = null;

    /**
     * Root of the entire hierarchy
     *
     * @return Model_Artifact
     */
    public static function root() {
        if (is_null(self::$_root)) {
            self::$_root = new Model_Artifact();
            self::$_root->projectRegistry = new theProjectRegistry();
        }
        return self::$_root;
    }

    /**
     * Attach sub-artifact if it's not here already
     *
     * @param string Name of the property to be accessed later
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return $this
     */
    protected function _attach($name, Model_Artifact_Interface $artifact, $property = null) {
        if (!isset($this->$name)) 
            $this->$name = $artifact;
        $this->_initialize($this->$name, $property);
        return $this;
    }
    
    /**
     * Attach sub-artifact if it's not here already, as an item in array
     *
     * @param string Key of the array
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return $this
     */
    protected function _attachItem($key, Model_Artifact_Interface $artifact, $property = null) {
        if (!isset($this[$key]))
            $this[$key] = $artifact;
        $this->_initialize($this[$key], $property);
        return $this;
    }
    
    /**
     * Initialize child artifact
     *
     * @param Model_Artifact_Interface The artifact to attach
     * @param string Property to set with $this
     * @return void
     */
    protected function _initialize(Model_Artifact_Interface $artifact, $property) {
        if (is_null($property))
            $artifact->ps()->parent = $this; // TODO: this should be removed and implemented in FaZend
        else {
            if (method_exists($artifact, $property))
                $artifact->$property($this);
            else
                $artifact->$property = $this;
        }
    }
    
    /**
     * To be implemented in FaZend_POS_Abstract
     *
     * @return void
     */
    public function __construct() {
        $this->_init();
    }
    
    /**
     * To be implemented in FaZend_POS_Abstract
     *
     * @return void
     */
    protected function _init() {
    }
    
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
    
}
