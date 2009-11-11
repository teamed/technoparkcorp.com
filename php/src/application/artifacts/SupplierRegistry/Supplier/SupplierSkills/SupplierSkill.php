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
 * One skill for a supplier, with a grade
 *
 * @package Artifacts
 */
class theSupplierSkill {

    /**
     * Name of the skill
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Grade
     *
     * @var integer
     */
    protected $_grade;

    /**
     * Create new skill
     *
     * @param string Text name of the skill
     * @param integer Grade of the skill
     * @return void
     **/
    public function __construct($name, $grade) {
        validate()
            ->regex($name, '/^[\w\d]+$/', "Skill name should contain only numbers and letters, but '{$name}' provided")
            ->type($grade, 'integer', "Skill grade must be INTEGER")
            ->true($grade <= 100 && $grade >= 0, "Grade must be in [0..100] interval, {$grade} provided");
        
        $this->_name = $name;
        $this->_grade = $grade;
    }

    /**
     * Show it as a string
     *
     * @return void
     **/
    public function __toString() {
        return $this->_name . '/' . $this->_grade . '%';
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
     * Get list of default levels
     *
     * @return array
     **/
    public static function getDefaultLevels() {
        return array(
            25 => 'beginner',
            50 => 'average',
            75 => 'intermediate',
            100 => 'professional',
        );
    }

    /**
     * How compliant this skill is to the given skill?
     *
     * @param theSupplierSkill Skill to be compliant to
     * @return integer Percents in [0..100] interval
     **/
    public function getCompliance(theSupplierSkill $skill) {
        return 100 - abs($this->grade - $skill->grade);
    }

    /**
     * Is it the same skill?
     *
     * @param theSupplierSkill Skill to be compliant to
     * @return boolean
     **/
    public function isSameSkill(theSupplierSkill $skill) {
        return $this->name == $skill->name;
    }

}
