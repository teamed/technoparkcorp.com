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
     * @param string ISO-3166 two-letter country code
     * @return theSupplier
     **/
    public static function create($email, $name, $country) {
        validate()
            ->emailAddress($email, array(), "Invalid format of supplier's email")
            ->false(empty($name), "Name of supplier may not be empty")
            ->countryCode($country, "Invalid country code, two-letters ISO 3166 required");
        
        $supplier = new theSupplier();
        $supplier->email = $email;
        $supplier->name = $name;
        $supplier->country = $country;
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
     * @param string Why you are doing this operation?
     * @return theSupplierAbility
     **/
    public function createAbility($role, $price, $reason) {
        $ability = theSupplierAbility::create($this, $role, new Model_Cost($price));
        $this->createRecord("Ability added: '{$ability->role}', price: {$ability->price}\nReason: " . $reason);
        return $ability;
    }

    /**
     * Create new skill for this supplier
     *
     * @param string Name of the skill
     * @param integer Level
     * @param string Why you are doing this operation?
     * @return theSupplierSkill
     **/
    public function createSkill($name, $level, $reason) {
        $skill = theSupplierSkill::create($this, $name, intval($level));
        $this->createRecord("Skill added: '{$name}', level: {$level}\nReason: " . $reason);
        return $skill;
    }

    /**
     * Create new record for this supplier
     *
     * @param string Text of the record
     * @param string Why you are doing this operation?
     * @return theSupplierAbility
     **/
    public function createRecord($text, $file = null) {
        return theSupplierRecord::create($this, $text, $file);
    }
    
    /**
     * Delete supplier's ability
     *
     * @param theSupplierAbility The ability to delete
     * @param string Why you are doing this operation?
     * @return void
     **/
    public function deleteAbility(theSupplierAbility $ability, $reason) {
        $this->createRecord("Ability deleted: '{$ability->role}', price: {$ability->price}\nReason: " . $reason);
        $ability->delete();
    }

    /**
     * Delete supplier's skill
     *
     * @param theSupplierSkill The skill to delete
     * @param string Why you are doing this operation?
     * @return void
     **/
    public function deleteSkill(theSupplierSkill $skill, $reason) {
        $this->createRecord("Skill deleted: '{$skill->name}', level: {$skill->level}\nReason: " . $reason);
        $skill->delete();
    }
    
    /**
     * Find and return an ability by ID
     *
     * @param integer ID of the ability
     * @return theSupplierAbility
     **/
    public function findAbilityById($id) {
        return new theSupplierAbility(intval($id));
    }

    /**
     * Find and return a skill by ID
     *
     * @param integer ID of the skill
     * @return theSupplierAbility
     **/
    public function findSkillById($id) {
        return new theSupplierSkill(intval($id));
    }

    /**
     * Return a list of records
     *
     * To be called as:
     * <code>
     * $list = $supplier->records;
     * </code>
     *
     * @return theSupplierRecord[]
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
     * @return theSupplierAbility[]
     **/
    protected function _getAbilities() {
        return theSupplierAbility::retrieveBySupplier($this);
    }

    /**
     * Return a list of skills
     *
     * To be called as:
     * <code>
     * $list = $supplier->skills;
     * </code>
     *
     * @return theSupplierSkill[]
     **/
    protected function _getSkills() {
        return theSupplierSkill::retrieveBySupplier($this);
    }

    /**
     * Return a name of country
     *
     * @return string
     **/
    protected function _getCountryName() {
        return Zend_Locale::getTranslation($this->country, 'territory');
    }

}
