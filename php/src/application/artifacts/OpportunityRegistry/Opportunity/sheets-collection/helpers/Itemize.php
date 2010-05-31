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
 * @version $Id: Vision.php 651 2010-02-10 17:50:05Z yegor256@yahoo.com $
 *
 */

/**
 * @see FaZend_View_Helper
 */
require_once 'FaZend/View/Helper.php';

/**
 * TeX list
 *
 * @package Artifacts
 */
class Sheet_Helper_Itemize extends FaZend_View_Helper
{

    /**
     * List in TeX
     *
     * @param mixed Array of items, SimpleXMLElement or just array
     * @param string Type of itemized list
     * @param array Options, optional
     * @return SimpleXMLElement|mixed
     * @throws Sheet_Helper_Itemize_InvalidStyleException
     */
    public function itemize($collection, $style = 'itemize', array $options = array()) 
    {
        $items = $this->_deriveList($collection, $options);
        switch ($style) {
            case 'itemize':
            case 'enumerate':
                if (!count($items)) {
                    $tex = "\\textit{empty...}";
                } else {
                    $tex = "\\begin{{$style}}\n";
                    foreach ($items as $name=>$value) {
                        $tex .= sprintf(
                            "\t\\item %s\n",
                            $value
                        );
                    }
                    $tex .= "\\end{{$style}}";
                }
                break;

            case 'description':
                if (!count($items)) {
                    $tex = "\\textit{empty...}";
                } else {
                    $tex = "\\begin{{$style}}\n";
                    foreach ($items as $name=>$value) {
                        $tex .= sprintf(
                            "\t\\item[%s] %s\n",
                            $name,
                            $value
                        );
                    }
                    $tex .= "\\end{{$style}}";
                }
                break;
        
            case 'inline':
                if (!empty($options['mask'])) {
                    $mask = FaZend_Callback::factory($options['mask']);
                    foreach ($items as $name=>&$value) {
                        $value = $mask->call($name, $value);
                    }
                }
                switch (true) {
                    case !count($items):
                        $tex = "$\\dots$";
                        break;
                    case count($items) < 2:
                        $tex = implode('', $items);
                        break;
                    case count($items) == 2:
                        $tex = implode(' and ', $items);
                        break;
                    default:
                        $tex = implode(",\n\t", array_slice($items, 0, -1)) .
                        ', and ' . array_pop($items);
                        break;
                }
                break;
        
            case 'inparaenum':
                if (!count($items)) {
                    $tex = "$\\dots$";
                } else {
                    $tex = "\\begin{inparaenum}[\\itshape a\\upshape)]\n\t" .
                    implode(";\n\t\\item ", $items) .
                    "\n\\end{inparaenum}";
                }
                break;

            default:
                FaZend_Exception::raise(
                    'Sheet_Helper_Itemize_InvalidStyleException', 
                    "Style '{$style}' is unknown"
                );
            
        }
        return $tex;
    }
    
    /**
     * Derive array of items from XML
     *
     * @param SimpleXMLElement|mixed
     * @param array List of options, if provided
     * @return string[]
     */
    protected function _deriveList($collection, array $options) 
    {
        $items = array();
        foreach ($collection as $name=>$item) {
            if (is_scalar($item)) {
                $items[$name] = $item;
            } else {
                $items[strval($item['name'])] = strval($item['value']);
            }
        }
        
        if (empty($options['rawTex'])) {
            $temp = array();
            foreach ($items as $key=>$value) {
                $temp[$this->getView()->tex($key)] = $this->getView()->tex($value);
            }
            $items = $temp;
        }
        return $items;
    }

}
