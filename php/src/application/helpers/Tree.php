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
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Dynamic TREE
 *
 * @package helpers
 */
class Helper_Tree extends FaZend_View_Helper
{

    /**
     * Collection of objects
     *
     * @var array
     */
    protected $_collection = array();
    
    /**
     * Name of ID column in every element
     *
     * @var string|false FALSE means that __toString() shall be used
     * @see __construct()
     */
    protected $_id;
    
    /**
     * Separator in the ID, to build levels
     *
     * @var string
     * @see __construct()
     */
    protected $_separator;
    
    /**
     * Attribs of the DIV
     *
     * @var array
     */
    protected $_attribs;
    
    /**
     * Mask to use with sprintf()
     *
     * @var string
     */
    protected $_mask = 'empty';
    
    /**
     * List of tokens to pass to sprintf()
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * Builds an instance of this class
     *
     * @return Helper_Tree
     */
    public function tree()
    {
        $this->getView()->includeJQuery();
        return $this;
    }

    /**
     * Set collection
     *
     * @param mixed
     * @param string Name of ID in every element
     * @param string Separator of sectors
     * @return $this
     */
    public function setCollection($collection, $id = false, $separator = '/')
    {
        if (!is_array($collection))
            $collection = iterator_to_array($collection);
        $this->_collection = $collection;
        $this->_id = $id;
        $this->_separator = $separator;
        return $this;
    }
    
    /**
     * Set mask
     *
     * @param string Mask to use
     * @return $this
     */
    public function setMask($mask) 
    {
        $this->_mask = $mask;
        return $this;
    }
    
    /**
     * Add new token
     *
     * @param mixed
     * @return $this
     */
    public function addToken($token) 
    {
        $this->_tokens[] = $token;
        return $this;
    }
    
    /**
     * Add new attribute
     *
     * @param string Name
     * @param string Value
     * @return $this
     */
    public function addAttrib($name, $value) 
    {
        $this->_attribs[$name] = $value;
        return $this;
    }

    /**
     * Render the helper
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string)$this->_render();
        } catch (Exception $e) {
            return get_class($this) . ' throws ' . get_class($e) . ': ' . $e->getMessage();
        }
    }

    /**
     * Render the helper
     *
     * @return string
     */
    protected function _render()
    {
        $new = array();
        foreach ($this->_collection as $id=>$item) {
            if ($this->_id) {
                $id = $item->{$this->_id};
            } else {
                $id = strval($item);
            }
            $new[$id] = $item;
        }
        
        ksort($new);
        $this->_collection = $new;
        $this->_divCounter = 0;
        
        $attribs = '';
        foreach ($this->_attribs as $name=>$value)
            $attribs .= ' ' . $name . '="' . $value . '"';
        
        return "<div{$attribs}>\n{$this->_renderNode()}\n</div>";
    }
    
    /**
     * Build list in HTML, recursively
     *
     * @param string Root name
     * @return void
     */
    protected function _renderNode($root = '') 
    {
        if ($root)
            $indent = str_repeat("\t", substr_count($root, $this->_separator) + 1);
        else
            $indent = '';
        
        $html = '';
        while ($item = current($this->_collection)) {
            $id = key($this->_collection);
            
            if ($root && strpos($id, $root) !== 0) {
                break;
            }

            if ($root)
                $id = substr($id, strlen($root) + 1);
            
            $sectors = explode($this->_separator, $id);

            // is it a chapter?
            if (count($sectors) > 1) {
                $html .= sprintf(
                    "%s<span onclick=\"$('div#tree%d').toggle();\">%s</span>\n%s<div id=\"tree%d\">\n",
                    $indent,
                    ++$this->_divCounter,
                    $sectors[0],
                    $indent,
                    $this->_divCounter
                );

                $html .= $this->_renderNode(
                    ($root ? $root . $this->_separator : false) . $sectors[0]
                ) . "{$indent}</div>\n";
                continue;
            }
            
            $values = array();
            foreach ($this->_tokens as $token) {
                eval("\$value = \$item{$token};");
                $values[] = $value;
            }

            $html .= 
            "{$indent}<div>" . call_user_func_array(
                'sprintf',
                array_merge(
                    array($this->_mask),
                    $values
                )
            ) . "</div>\n";

            next($this->_collection);
        }
        return $html;
    }

}
