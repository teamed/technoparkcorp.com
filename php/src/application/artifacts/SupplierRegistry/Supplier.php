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
 * One supplier
 *
 * @package Artifacts
 */
class theSupplier extends FaZend_Db_Table_ActiveRow_supplier implements Model_Artifact_Interface {

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
            
        return parent::__get($name);
    }
    
    /**
     * Create new supplier
     *
     * @param string Email of the supplier
     * @param string Full name of he/she
     * @return theSupplier
     **/
    public static function create($email, $name) {
        validate()
            ->emailAddress($email, array(), "Invalid format of supplier's email")
            ->false(empty($name), "Name of supplier may not be empty");
        
        $supplier = new theSupplier();
        $supplier->email = $email;
        $supplier->name = $name;
        $supplier->save();
        return $supplier;
    }

    /**
     * Find one supplier by email
     *
     * @param string Email address of the supplier to find
     * @return theSupplier
     **/
    public static function findByEmail($email) {
        return self::retrieve()
            ->where('email = ?', $email)
            ->setRowClass('theSupplier')
            ->fetchRow();
    }

    /**
     * Return a full list of suppliers
     *
     * @return theSupplier[]
     */
    public static function retrieveAll() {
        return self::retrieve()
            ->setRowClass('theSupplier')
            ->fetchAll();
    }
    
    /**
     * Create new ability for this supplier
     *
     * @param string Role to assign
     * @param string Price per hour
     * @return theSupplierAbility
     **/
    public function createAbility($role, $price) {
        return theSupplierAbility::create($this, $role, new Model_Cost($price));
    }

    /**
     * Create new record for this supplier
     *
     * @param string Text of the record
     * @param string Absolute file name
     * @return theSupplierAbility
     **/
    public function createRecord($text, $file = null) {
        return theSupplierRecord::create($this, $text, $file);
    }

    /**
     * Return a list of records
     *
     * To be called as:
     * <code>
     * $list = $supplier->records;
     * </code>
     *
     * @return void
     **/
    protected function _getRecords() {
        return theSupplierRecord::retrieveBySupplier($this);
    }

    /**
     * Return a list of abilities
     *
     * To be called as:
     * <code>
     * $list = $supplier->abilities;
     * </code>
     *
     * @return void
     **/
    protected function _getAbilities() {
        return theSupplierAbility::retrieveBySupplier($this);
    }

}
