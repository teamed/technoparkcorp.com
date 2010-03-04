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
 * SAO diagram
 *
 * @package Artifacts
 */
class Sheet_SAO_Diagram
{
    
    /**
     * Options
     *
     * @var array
     */
    protected $_options = array(
        'width' => 100,
        'height' => 100,
        'texComponent' => '\\saoComponent',
        'texBoundary' => '\\saoBoundary',
        'texConnector' => '\\saoConnector',
    );
    
    /**
     * List of components
     *
     * @var string
     */
    protected $_components = array();
    
    /**
     * Links between components
     *
     * Every item is an array with three items: from, to and text.
     *
     * @var array[]
     */
    protected $_links = array();
    
    /**
     * Add new component
     *
     * @param string Component name
     * @return void
     */
    public function addComponent($component) 
    {
        $id = 1;
        foreach (array_keys($this->_components) as $exists) {
            $id = max($id, intval(substr($exists, 1))) + 1;
        }
        $this->_components['c' . $id] = $component;
    }
    
    /**
     * Add new link
     *
     * @param string From component name
     * @param string To component name
     * @param string Text of the link, if any
     * @return void
     * @throws Sheet_SAO_Diagram_InvalidComponentException
     */
    public function addLink($from, $to, $text) 
    {
        if (!in_array($from, $this->_components)) {
            FaZend_Exception::raise(
                'Sheet_SAO_Diagram_InvalidComponentException',
                "Component not found: '{$from}'"
            );
        }
        if (!in_array($to, $this->_components)) {
            FaZend_Exception::raise(
                'Sheet_SAO_Diagram_InvalidComponentException',
                "Component not found: '{$to}'"
            );
        }
        $this->_links[] = array(
            array_search($from, $this->_components), 
            array_search($to, $this->_components), 
            $text
        );
    }
    
    /**
     * Set array of config options
     *
     * @param array Options
     * @return $this
     * @throws Sheet_SAO_InvalidOption
     */
    public function setOptions(array $options) 
    {
        foreach ($options as $option=>$value) {
            if (!array_key_exists($option, $this->_options)) {
                FaZend_Exception::raise(
                    'Sheet_SAO_InvalidOption', 
                    "Option '{$option}' is unknown in the diagram builder"
                );
            }
            $this->_options[$option] = $value;
        }
        return $this;
    }
    
    /**
     * Convert diagram to LaTeX
     *
     * @param Zend_View View to render
     * @return string
     */
    public function getLatex(Zend_View $view) 
    {
        // normalize options before rendering
        $this->_normalizeOptions();
        
        // center of the diagram
        $centerX = $this->_options['width'] / 2;
        $centerY = $this->_options['height'] / 2;

        // radius for components allocation
        $radiusX = $centerX * 0.8;
        $radiusY = $centerY * 0.8;

        $angle = 0;
        $angleDelta = pi() * 2 / count($this->_components);
        $tex ="\\begin{tikzpicture}\n";
        foreach ($this->_components as $id=>$component) {
            $angle += $angleDelta;
            $x = $centerX + $radiusX * cos($angle);
            $y = $centerY + $radiusY * sin($angle);
            $tex .= "{$this->_options['texComponent']}{{$id}}{{$x}}{{$y}}\n\t{{$view->tex($component)}}\n";
        }
         
        foreach ($this->_links as $link) {
            list($from, $to, $text) = $link;
            
            $tex .= "{$this->_options['texConnector']}{{$from}}{{$to}}{{$text}}\n";
        }
        
        $tex .=
        "{$this->_options['texBoundary']}{(" . implode(') (', array_keys($this->_components)). ")}\n";

        return $tex . "\\end{tikzpicture}\n";
    }
    
    /**
     * Normalize them
     *
     * @return void
     */
    protected function _normalizeOptions() 
    {
    }
        
}
