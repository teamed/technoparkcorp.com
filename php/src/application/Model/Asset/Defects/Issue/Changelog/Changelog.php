<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One changelog holder
 *
 * @package Model
 */
class Model_Asset_Defects_Issue_Changelog_Changelog
{

    /**
     * List of fields
     *
     * @var Model_Asset_Defects_Issue_Changelog_Field[]
     * @see get()
     * @see __construct()
     */
    protected $_fields = array();

    /**
     * Initialize the internal structure
     *
     * @return void
     */
    public function __construct()
    {
        foreach (Model_Asset_Defects_Issue_Changelog_Field_Abstract::getAllTypes() as $type)
            $this->_fields[$type] = false;
    }
    
    /**
     * Allows to change/store this field
     *
     * @param string Name of the field
     * @return boolean
     */
    public function allowsField($name)
    {
        return isset($this->_fields[$name]);
    }

    /**
     * Get current value of certain field
     *
     * @param string Name of field
     * @return mixed
     * @throws Model_Asset_Defects_Issue_Changelog_FieldNotFound
     */
    public function get($name)
    {
        if (!$this->allowsField($name)) {
            FaZend_Exception::raise(
                'Model_Asset_Defects_Issue_Changelog_FieldNotFound',
                "There is no such field in changelog like '{$name}'"
            );
        }
                
        if ($this->_fields[$name] === false) {
            $className = 'Model_Asset_Defects_Issue_Changelog_Field_' . ucfirst($name);
            $this->_fields[$name] = new $className();
        }
        
        return $this->_fields[$name];
    }

    /**
     * Set new value of certain field
     *
     * @param string Name of field
     * @param mixed Value to set
     * @return $this
     */
    public function set($name, $value)
    {
        $this->get($name)->setValue($value);
        return $this;
    }

    /**
     * Load value into field from pre-existing source (tracker)
     *
     * @param string Name of field
     * @param mixed Value to set
     * @param string Changer of the field (email)
     * @param Zend_Date When this change happened
     * @return $this
     */
    public function load($name, $value, $author, Zend_Date $date)
    {
        $this->get($name)->load($value, $author, $date);
        return $this;
    }

    /**
     * Get list of changes to make
     *
     * Returns a list of fields and values to be changed in tracker.
     *
     * @return array
     * @see Model_Asset_Defects_Issue_Trac::_saveChangelog()
     */
    public function whatToSave()
    {
        $list = array();
        foreach ($this->_fields as $name=>$field) {
            if (($field !== false) && $field->wasChanged())
                $list[$name] = $field->getValue();
        }
        return $list;
    }
    
}
