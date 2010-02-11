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
 * @version $Id$
 *
 */

/**
 * TeXry.com interface
 *
 * @package Model
 */
class Model_Texry
{
    
    /**
     * List of dirs where templates are located
     *
     * @var string[]
     */
    protected static $_dirs = array();
    
    /**
     * View for template rendering
     *
     * @var Zend_View
     */
    protected $_view;
    
    /**
     * Name of template
     *
     * @var string
     */
    protected $_template;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($template)
    {
        $this->_template = $template;
        $this->_view = new Zend_View();
        $this->_view->addScriptPath(self::$_dirs);
    }
    
    /**
     * Add new directory
     *
     * @param string Absolute path
     * @return void
     */
    public static function addTemplateDir($dir)
    {
        self::$_dirs[] = $dir;
    }
    
    /**
     * Inject value into View
     *
     * @param string Name
     * @param string Value
     * @return $this
     */
    public function assign($name, $value) 
    {
        $this->_view->assign($name, $value);
        return $this;
    }
    
    /**
     * Render and return TeX code
     *
     * @return string TeX code
     */
    public function render() 
    {
        return $this->_view->render($this->_template);
    }

}
