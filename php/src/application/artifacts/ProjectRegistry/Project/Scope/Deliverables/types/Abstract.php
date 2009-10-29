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
     * List of attributes
     *
     * @var theDeliverableAttributes
     */
    protected $_attributes;

    /**
     * Static plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_loader;
     
    /**
     * Create new deliverable
     *
     * @param string Name of the deliverable
     * @param string Text description of it
     * @return Deliverables_Abstract
     **/
    public static function factory($type, $name, $description) {
        if (!isset(self::$_loader)) {
            self::$_loader = new Zend_Loader_PluginLoader(array('Deliverables_' => 
                dirname(__FILE__)));
        }
        // load this deliverable, if possible
        $className = self::$_loader->load($type);
        return new $className($name, $description);        
    }
     
    /**
     * Construct the class
     *
     * @param string Name of the deliverable
     * @param string Text description of it
     * @return void
     */
    protected function __construct($name, $description) {
        $this->_name = $name;
        $this->_description = $description;
        $this->_attributes = new theDeliverableAttributes();
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
     * Return type of this deliverable
     *
     * @return string
     **/
    protected function _getType() {
        return lcfirst(preg_replace('/^Deliverables_/', '', get_class($this)));
    }
    
}
