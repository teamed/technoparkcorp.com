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
 * Single component of the system
 *
 * @package Artifacts
 */
class theComponent implements Model_Artifact_Stateless {
    
    const TYPE_CLASS = 1;
    const TYPE_FILE = 2;
    const TYPE_METHOD = 3;
    const TYPE_PACKAGE = 4;
    
    /**
     * Full name of the component
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Type
     *
     * @var integer
     */
    protected $_type;
    
    /**
     * Create new component
     *
     * @param string Full name of the component
     * @param string Type of it
     * @return void
     **/
    public function __construct($name, $type) {
        $this->_name = $name;
        $this->_type = $type;
    }
    
    /**
     * Getter
     *
     * @return mixed
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
    
}
