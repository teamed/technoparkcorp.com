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
 * Collection of suppliers
 *
 * @package Artifacts
 */
class theSupplierRegistry implements Model_Artifact_Interface {

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
     * Create new supplier and return him/her
     *
     * @param string Email of the supplier
     * @param string Full name of he/she
     * @return theSupplier
     **/
    public function createSupplier($email, $name) {
        return theSupplier::create($email, $name);
    }
    
    /**
     * Find one supplier by email
     *
     * @param string Email address of the supplier to find
     * @return theSupplier
     **/
    public static function findByEmail($email) {
        return theSupplier::findByEmail($email);
    }
    
    /**
     * Get full list of suppliers
     *
     * @return theSupplier[]
     **/
    public function getEverybody() {
        return theSupplier::retrieveAll();
    }
    
    /**
     * Get full list of supplier roles
     *
     * @return theSupplierRole[]
     **/
    public function getRoles() {
        return theSupplierRole::retrieveAll();
    }
    
    
}
