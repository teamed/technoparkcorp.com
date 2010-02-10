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
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array();

    /**
     * Configuration
     *
     * @var SimpleXMLElement
     */
    protected $_config;
    
    /**
     * Collection of sheets
     *
     * @var theSheetsCollection
     */
    protected $_sheets = null;

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
     * Inject dependency
     *
     * @param theSheetsCollection
     * @return $this
     * @see theSheetsCollection::offsetSet()
     */
    public function setSheetsCollection(theSheetsCollection $sheets) 
    {
        $this->_sheets = $sheets;
        return $this;
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
        if (method_exists($this, $method)) {
            return $this->$method();
        }
            
        $var = '_' . $name;
        if (property_exists($this, $var)) {
            return $this->$var;
        }
        
        $items = $this->_config->xpath("//item[@name='{$name}']");
        if (count($items) > 0) {
            if (isset($items[0]['value'])) {
                return strval($items[0]['value']);
            } else {
                return $items[0];
            }
        }
        
        if (array_key_exists($name, $this->_defaults)) {
            return $this->_defaults[$name];
        }
        
        FaZend_Exception::raise(
            'Sheet_Abstract_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Get sheet in latex
     *
     * @return string LaTeX source
     * @throws Sheet_Abstract_RenderingProhibited
     */
    public function getLatex() 
    {
        if (is_null($this->sheets)) {
            FaZend_Exception::raise(
                'Sheet_Abstract_RenderingProhibited', 
                "You can't render '{$this->name}' outside of collection"
            );
        }
        return $this->sheets->getView()
            ->assign('sheet', $this)
            ->render($this->getTemplateFile());
    }
    
    /**
     * Get short proposal paragraph
     *
     * @return string LaTeX source
     * @throws Sheet_Abstract_RenderingProhibited
     */
    public function getProposal() 
    {
        if (is_null($this->sheets)) {
            FaZend_Exception::raise(
                'Sheet_Abstract_RenderingProhibited', 
                "You can't render '{$this->name}' outside of collection"
            );
        }
        return $this->sheets->getView()->render('proposals/' . $this->getTemplateFile());
    }
    
    /**
     * Get name of sheet, like "Vision", "ROM", etc.
     *
     * @return string
     */
    public function getSheetName() 
    {
        return preg_replace('/Sheet_/', '', get_class($this));
    }
    
    /**
     * Get name of the template file, like "Vision.tex", "ROM.tex", etc.
     *
     * @return string
     */
    public function getTemplateFile() 
    {
        return $this->getSheetName() . '.tex';
    }
    
}
