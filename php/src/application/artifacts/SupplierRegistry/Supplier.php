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
class theSupplier extends Model_Artifact {

    /**
     * Email
     *
     * @var string
     */
    protected $_email;
    
    /**
     * Full name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Country of location
     *
     * @var string
     */
    protected $_country;
    
    /**
     * List of skills
     *
     * @var theSupplierSkills
     */
    protected $_skills;
    
    /**
     * List of roles
     *
     * @var theSupplierRole[]
     */
    protected $_roles;
    
    /**
     * List of attachments
     *
     * @var Model_Artifact_Attachments
     */
    protected $_attachments;
    
    /**
     * Construct the class
     *
     * @param string Email
     * @param string Name
     * @return void
     */
    public function __construct($email, $name) {
        $this->setEmail($email);
        $this->setName($name);
        
        $this->_skills = new theSupplierSkills();
        $this->_roles = new theSupplierRoles();
        $this->_attachments = new Model_Artifact_Attachments();
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('Supplier_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));
    }
    
    /**
     * Set email
     *
     * @param string Email
     * @return void
     **/
    public function setEmail($email) {
        validate()
            ->emailAddress($email, array(), "Invalid format of supplier's email: {$email}");
        $this->_email = $email;
        return $this;
    }

    /**
     * Set name
     *
     * @param string Full name of supplier
     * @return void
     **/
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    /**
     * Set country
     *
     * @param string Two-letters name of the country
     * @return void
     **/
    public function setCountry($country) {
        validate()
            ->countryCode($country, "Invalid format of supplier's country: {$country}");
        $this->_country = $country;
        return $this;
    }

    /**
     * Add skill
     *
     * @param theSupplierSkill Skill required, with grade
     * @return void
     **/
    public function addSkill(theSupplierSkill $skill) {
        $this->_skills[] = $skill;
        return $this;
    }

    /**
     * Add role
     *
     * @param theSupplierRole Role to add
     * @return void
     **/
    public function addRole(theSupplierRole $role) {
        $this->_roles[] = $role;
        return $this;
    }

    /**
     * Add attachment
     *
     * @param Model_Artifact_Attachments_Attachment Attachment to add
     * @return void
     **/
    public function addAttachment(Model_Artifact_Attachments_Attachment $attachment) {
        $this->_attachments[] = $attachment;
        return $this;
    }

    /**
     * Create role, interface for PAGES
     *
     * @param string Name of the role
     * @param string Price
     * @return void
     **/
    public function createRole($role, $price) {
        return $this->addRole(new theSupplierRole($role, new Model_Cost($price)));
    }
    
    /**
     * Create skill, interface for PAGES
     *
     * @param string Name of the skill
     * @param string Grade
     * @return void
     **/
    public function createSkill($name, $grade) {
        return $this->addSkill(new theSupplierSkill($name, intval($grade)));
    }
    
    /**
     * Create attachment, interface for PAGES
     *
     * @param string Name of the attachment
     * @param string Description
     * @param string Absolute file name
     * @return void
     **/
    public function createAttachment($name, $description, $file) {
        return $this->addAttachment(new Model_Artifact_Attachments_Attachment($name, $description, $file));
    }
    
    /**
     * Returns country full name
     *
     * @return string
     **/
    protected function _getCountryName() {
        return Zend_Locale::getTranslation($this->country, 'territory');
    }
    
}
