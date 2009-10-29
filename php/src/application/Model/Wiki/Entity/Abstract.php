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
 * One abstract wiki entity
 *
 * @package Model
 */
abstract class Model_Wiki_Entity_Abstract {

    /**
     * Name of the entity
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * Text description
     *
     * @var string
     */
    protected $_description;
    
    /**
     * Attributes
     *
     * @var string[]
     */
    protected $_attributes = array();
    
    /**
	 * Constructor
     *
     * @param Model_Wiki_Abstract Wiki storage instance
     * @param string Unique name of the entity
     * @param string Text description of it
     * @return void
     */
	public function __construct($name, $description) {
	    $this->_name = $name;
	    $this->_description = $description;
    }

    /**
     * Is it a name of entity? Get its prefix
     *
     * @param string Name of some entity
     * @return string Prefix recognized
     **/
    public static function getEntityPrefix($name) {
        if (!preg_match('/^(R|QOS)\d+(?:\.\d+)*|(If|Actor)[A-Z]\w+$/', $name, $matches))
            return false;
        return $matches[1] ? $matches[1] : $matches[2];
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
        
        FaZend_Exception::raise('Model_Wiki_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));        
    }
    
    /**
     * Set one attribute
     *
     * @return $this
     **/
    public function setAttribute($name, $value = true) {
        $this->_attributes[$name] = $value;
        return $this;
    }
    
    /**
     * Derive all known attributes
     *
     * @param theDeliverableAttributes This class will be filled with values
     * @return void
     **/
    public function deriveDetails(Deliverables_Abstract $deliverable) {
        foreach (array_keys(array_filter($this->_attributes)) as $attrib) {
            switch (true) {
                case $attrib == 'out':
                    $deliverable->setOutOfScope();
                    break;
                    
                case $attrib == 'must':
                    $deliverable->setImportance(5);
                    break;
                    
                case preg_match('/^i(\d+)$/i', $attrib, $matches):
                    $deliverable->setImportance($matches[1]);
                    break;
                    
                case preg_match('/^c(\d+)$/i', $attrib, $matches):
                    $deliverable->setComplexity($matches[1]);
                    break;

                default:
                    $deliverable->setProtocol(
                        $deliverable->protocol . 
                        ($deliverable->protocol ? '; ' : false) . 
                        "Attribute '{$attrib}' ignored since it's not recognized");
                    
            }
        }
    }
    
    /**
     * Get type of this entity
     *
     * @return string
     **/
    protected function _getType() {
        $prefix = strtolower(self::getEntityPrefix($this->_name));
        switch ($prefix) {
            case 'actor':
                return 'actor';
            case 'r':
                return 'functional';
            case 'qos':
                return 'qos';
            case 'if':
                return 'interface';
            default:
                FaZend_Exception::raise('Model_Wiki_InvalidType', 
                    "Type of '{$this->_name}' is unknown ('{$prefix}') in " . get_class($this));        
        }
    }

}
