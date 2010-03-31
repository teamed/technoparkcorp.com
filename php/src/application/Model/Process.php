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
 * One process
 * 
 * @package Model
 * @see Model_Wobot_QC
 */
class Model_Process
{

    /**
     * List of names of all processes
     *
     * @var string[]
     */
    protected static $_names = array(
        'PMO',
        'Finance',
        'Security',
        'HR',
        'QA',
        'Marketing',
        'Sales'
    );
    
    /**
     * Name of the process
     *
     * @var string
     */
    protected $_name;

    /**
     * Get list of all processes
     *
     * @return Model_Process[]
     * @see Model_Wobot_QC::getAllNames()
     */
    public static function retrieveAll() 
    {
        $processes = new ArrayIterator();
        foreach (self::$_names as $name) {
            $processes[] = new self($name);
        }
        return $processes;
    }
    
    /**
     * Find process by name
     *
     * @param string Name of the process
     * @return Model_Process
     */
    public static function findByName($name) 
    {
        if (!in_array($name, self::$_names)) {
            FaZend_Exception::raise(
                'Model_Process_NotFound', 
                "Process '$name' not found"
            );
        }
        return new self($name);
    }
    
    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($name) 
    {
        $this->_name = $name;
    }

    /**
     * Text name of the process
     *
     * @return string
     */
    public function __toString() 
    {
        return $this->_name;
    }
    
    /**
     * Get project for this process
     *
     * @return Model_Project
     */
    public function getProject() 
    {
        return Model_Project::findByName($this->_name);
    }

    /**
     * Project exists for this process?
     *
     * @return boolean
     */
    public function projectExists() 
    {
        try {
            $this->getProject();
            return true;
        } catch (Shared_Project_NotFoundException $e) {
            assert($e instanceof Exception); // for ZCA and PHPMD
            return false;
        }
    }

}
