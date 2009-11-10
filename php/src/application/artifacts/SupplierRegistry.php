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
class theSupplierRegistry extends Model_Artifact {

    /**
     * TEMPORARY, delete it when using FaZend_POS
     *
     * @return void
     **/
    protected function _init() {
        $this->_attachItem(Model_Project_Test::PM, new theSupplier('Mr. Tester'));
    }

    /**
     * Return a list of needed people
     *
     * Array returned will have objects:
     * 
     *   ->role => 'programmer' // role required
     *   ->required => 5 // how many people required
     *   ->have => 4 // how many people we have
     *
     * @return array
     **/
    protected function _getWanted() {
        // this is going to be a result array
        $wanted = array();
        
        // get RAW data from projects and build result
        $raw = $this->_getWantedByProjects();
        foreach ($raw as $required) {
            if (!isset($wanted[$required['role']])) {
                $wanted[$required['role']] = FaZend_StdObject::create()
                    ->set('role', $required['role'])
                    ->set('required', 0)
                    ->set('have', count($this->retrieveByRole(
                        $required['role'], 
                        new Model_Cost($required['price']), 
                        $required['skills'])));
            }

            $wanted[$required['role']]->required++;
        }
        
        return $wanted;
    }
    
    /**
     * Return a list of needed people from project registry
     *
     * Array returned will have arrays:
     * 
     * array(
     *  'role' => 'programmer' // role
     *  'skills => array('PHP', 'XML') // list of skills required
     *  'price' => '15 USD' // maximum price
     * )
     *
     * @return array
     **/
    protected function _getWantedByProjects() {
        $ini = new Zend_Config_Ini(dirname(__FILE__) . '/SupplierRegistry/wanted.ini', 'wanted');
        return $ini->toArray();
    }
    
}
