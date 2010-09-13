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
 * @version $Id: Supplier.php 637 2010-02-09 09:49:56Z yegor256@yahoo.com $
 *
 */

/**
 * Collection of sales sheets
 *
 * @package Artifacts
 */
class theSheetsCollection implements ArrayAccess, Iterator, Countable
{

    /**
     * Array of sheets
     *
     * @var theSheet[]
     */
    protected $_sheets;
    
    /**
     * View for rendering of sheets
     *
     * @var Zend_View
     */
    protected $_view;
    
    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct() 
    {
        $this->_sheets = new ArrayIterator();
    }

    /**
     * Initialize autoloader, to be called from bootstrap
     *
     * @return void
     * @see bootstrap.php
     */
    public static function initAutoloader() 
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Sheet_');
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    get_include_path(),
                    dirname(__FILE__) . '/sheets-collection'
                )
            )
        );
        require_once dirname(__FILE__) . '/sheets-collection/Sheet/Abstract.php';
    }
    
    /**
     * Restore object after serialization
     *
     * @return void
     */
    public function __wakeup() 
    {
        $this->_view = null;
        foreach ($this->_sheets as $sheet) {
            $sheet->setSheetsCollection($this, false);
        }
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Opportunity_PropertyOrMethodNotFound
     */
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'SheetsCollection_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Method from ArrayAccess interface
     *
     * @param string Name of the sheet
     * @param Sheet_Abstract Sheet instance
     * @return void
     */
    public function add(Sheet_Abstract $sheet) 
    {
        validate()->true($sheet instanceof Sheet_Abstract);
        $sheet->setSheetsCollection($this);
        $this->_sheets[$sheet->getSheetName()] = $sheet;
    }

    /**
     * Get opportunity document in LaTeX
     *
     * @return string
     * @throws SheetsCollection_RootTemplateMissedException
     * @throws SheetsCollection_ContactsMissedException
     * @throws SheetsCollection_OfferMissedException
     */
    public function getLatex()
    {
        $template = 'binder.tex';
        if (!Sheet_Abstract::isTemplateExists($template)) {
            FaZend_Exception::raise(
                'SheetsCollection_RootTemplateMissedException', 
                "Template '{$template}' not found",
                'SheetsCollection_RenderingException'
            );
        }
        
        if (!isset($this['Offer'])) {
            FaZend_Exception::raise(
                'SheetsCollection_OfferMissedException', 
                "Sheet 'Offer' not found",
                'SheetsCollection_RenderingException'
            );
        }

        if (!isset($this['Contacts'])) {
            FaZend_Exception::raise(
                'SheetsCollection_ContactsMissedException', 
                "Sheet 'Contacts' not found",
                'SheetsCollection_RenderingException'
            );
        }
        try {
            return $this->getView()->render($template);
        } catch (Exception $e) {
            FaZend_Exception::raise(
                'SheetsCollection_UnknownException', 
                get_class($e) . ': ' . $e->getMessage(),
                'SheetsCollection_RenderingException'
            );
        }
    }
    
    /**
     * Get view
     *
     * @return Zend_View
     */
    public function getView() 
    {
        if (isset($this->_view)) {
            return $this->_view;
        }
        $this->_view = clone Zend_Registry::get('Zend_View');
        $this->_view
            ->addHelperPath(dirname(__FILE__) . '/sheets-collection/helpers', 'Sheet_Helper_')
            ->addScriptPath(dirname(__FILE__) . '/sheets-collection/templates')
            ->assign('sheets', $this);
        return $this->_view;
    }
    
    /**
     * Dump entire object into text presentation
     *
     * @return string
     */
    public function dump() 
    {
        $text = '';
        foreach ($this as $sheet) {
            $text .= sprintf(
                "%s\n\ttemplate: %s\n",
                $sheet->getSheetName(),
                $sheet->getTemplateFile() ? $sheet->getTemplateFile() : 'NULL'
            );
            $text .= "\tconfig:\n" . $this->_dumpXml($sheet->config, "\t\t");
            $text .= "\tcached:\n";
            foreach ($sheet->cached as $method=>$value) {
                $text .= sprintf(
                    "\t\t%s: %s\n",
                    $method,
                    $value
                );
            }
            $rc = new ReflectionClass($sheet);
            foreach ($rc->getProperties() as $property) {
                $name = substr($property->getName(), 1);
                if (in_array($name, array('xml', 'config', 'defaults', 'cached', 'sheets'))) {
                    continue;
                }
                $value = $sheet->$name;
                if (!is_scalar($value)) {
                    $value = str_replace("\n", "\n\t\t", print_r($value, true));
                }
                $text .= sprintf(
                    "\t%s: %s\n",
                    $property->getName(),
                    $value
                );
            }
        }
        return $text;
    }
    
    /**
     * Dump XML with multiple levels
     *
     * @param SimpleXMLElement 
     * @return string
     */
    protected function _dumpXml($xml, $prefix = "\t") 
    {
        $text = '';
        foreach ($xml->children() as $item) {
            $text .= sprintf(
                "%s%s: %s\n",
                $prefix,
                $item->attributes()->name,
                $item->attributes()->value
            );
            $text .= $this->_dumpXml($item, $prefix . "\t");
        }
        return $text;
    }
    
    /**
     * Method from Iterator interface
     *
     * @return void
     */
    public function rewind() 
    {
        return $this->_sheets->rewind();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     */
    public function next() 
    {
        return $this->_sheets->next();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     */
    public function key() 
    {
        return $this->_sheets->key();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     */
    public function valid() 
    {
        return $this->_sheets->valid();
    }

    /**
     * Method from Iterator interface
     *
     * @return void
     */
    public function current() 
    {
        return $this->_sheets->current();
    }

    /**
     * Method from Countable interface
     *
     * @return void
     */
    public function count() 
    {
        return $this->_sheets->count();
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     */
    public function offsetGet($name) 
    {
        return $this->_sheets->offsetGet($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @param string Name of the sheet
     * @param Sheet_Abstract Sheet instance
     * @return void
     */
    public function offsetSet($name, $sheet) 
    {
        return $this->_sheets->offsetSet($name, $sheet);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     */
    public function offsetExists($name) 
    {
        return $this->_sheets->offsetExists($name);
    }

    /**
     * Method from ArrayAccess interface
     *
     * @return void
     */
    public function offsetUnset($name) 
    {
        return $this->_sheets->offsetUnset($name);
    }
    
}
