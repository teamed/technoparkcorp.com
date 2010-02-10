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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id: Supplier.php 637 2010-02-09 09:49:56Z yegor256@yahoo.com $
 *
 */

/**
 * One abstract sheet
 *
 * @package Artifacts
 */
abstract class Sheet_Abstract
{

    /**
     * Configuration
     *
     * @var SimpleXMLElement
     */
    protected $_config;

    /**
     * Construct the class
     *
     * @param SimpleXMLElement Configuration
     * @return void
     */
    private function __construct(SimpleXMLElement $config) 
    {
        $this->_config = $config;
    }

    /**
     * Create new sheet and config it
     *
     * @param string Name of the sheet class
     * @param SimpleXMLElement Configuration
     * @return void
     * @throws Sheet_Abstract_InvalidNameException
     */
    public static function factory($name, SimpleXMLElement $config) 
    {
        if (!self::isValidName($name)) {
            FaZend_Exception::raise(
                'Sheet_Abstract_InvalidNameException', 
                "Invalid sheet name: '$name'"
            );
        } 
        
        $className = 'Sheet_' . ucfirst($name);
        return new $className($config);
    }
    
    /**
     * The name provided is valid?
     *
     * @param string Name of the sheet class
     * @return boolean
     */
    public static function isValidName($name) 
    {
        return file_exists(dirname(__FILE__) . '/' . ucfirst($name) . '.php');
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Opportunity_PropertyOrMethodNotFound
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'Sheet_Abstract_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
}
