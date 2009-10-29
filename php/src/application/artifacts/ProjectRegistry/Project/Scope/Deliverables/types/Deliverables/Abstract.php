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
 * One element in collection of deliverables
 *
 * @package Artifacts
 */
abstract class Deliverables_Abstract {
    
    /**
     * Name of this deliverable
     *
     * @var string
     */
    protected $_name;

    /**
     * Text description of this deliverable
     *
     * @var string
     */
    protected $_description;
    
    /**
     * Protocol of downloading from repository
     *
     * @var string
     */
    protected $_protocol = '';

    /**
     * Construct the class
     *
     * @param string Name of the deliverable
     * @param string Text description of it
     * @return void
     */
    public function __construct($name, $description) {
        $this->_name = $name;
        $this->_description = $description;
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
     * Call dispatcher
     *
     * @param string Name of the method
     * @param string Value to set
     * @return string
     **/
    public function __call($method, array $args) {
        if (substr($method, 0, 3) == 'set') {
            $property = '_' . lcfirst(substr($method, 3));
            if (property_exists($this, $property)) {
                if (count($args))
                    $value = array_shift($args);
                else
                    $value = true;
                $this->$property = $value;
                return $this;
            }
            FaZend_Exception::raise('PropertyNotFound', 
                "Can't find property '$property' in " . get_class($this));        
        }
        
        FaZend_Exception::raise('MethodNotFound', 
            "Can't find what is '$method' in " . get_class($this));        
    }
    
    /**
     * Return type of this deliverable
     *
     * @return string
     **/
    protected function _getType() {
        return lcfirst(preg_replace('/^Deliverables_/', '', get_class($this)));
    }
    
    /**
     * Get collection of ALL properties
     *
     * @return string
     **/
    protected function _getAttributes() {
        $reflector = new ReflectionObject($this);
        $data = array();
        foreach ($reflector->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            $name = $property->getName();
            if (in_array($name, array('_name', '_description')))
                continue;

            $name = substr($name, 1);
            $value = $this->$name;
            
            if (is_bool($value))
                $value = $value ? 'TRUE' : 'FALSE';
            $data[] = $name . '=' . $value;
        }
        return implode('; ', $data);
    }
    
}
