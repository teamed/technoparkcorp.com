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
     * List of options
     *
     * @var array
     */
    protected $_options = array(
        'startCollapsed' => false, // when loaded first time the tree is closed
        'useAjax' => false,
        'suffixOnly' => false, // section names will have only suffixes
        'renderSections' => true, // show sections? or just put links to items
    );
    
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
     * Set array of config options
     *
     * @param array Options
     * @return $this
     */
    public function setOptions(array $options) 
    {
        foreach ($options as $option=>$value) {
            if (!array_key_exists($option, $this->_options)) {
                FaZend_Exception::raise(
                    'Helper_Tree_InvalidOption', 
                    "Option '{$option}' in unknown the helper"
                );
            }
            $this->_options[$option] = $value;
        }
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
        $this->getView()->includeJQuery();
        
        // configure CSS for this helper
        $this->getView()->includeCSS('helper/tree.css');

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
        // doesn't work so far.
        $this->_options['useAjax'] = false;

        // normalize options
        if (!empty($this->_options['useAjax'])) {
            $this->_options['startCollapsed'] = true;
        }
        
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
        
        if (!empty($this->_options['useAjax'])) {
            $node = $this->getView()->post('node');
            if ($node) {
                echo $this->_renderNode($node);
                die();
            }
            
            $this->getView()->includeJQuery();
            $this->getView()->headScript()->appendScript(
                '
                function loadNode(div, node)
                {
                    if (div.html().length < 2) {
                        div.load("?test", {"node": node});
                    }
                }
                '
            );
        }

        $attribs = '';
        foreach ($this->_attribs as $name=>$value)
            $attribs .= ' ' . $name . '="' . $value . '"';
            
        return "<div{$attribs}>\n{$this->_renderNode()}\n</div>";
    }
    
    /**
     * Build list in HTML, recursively
     *
     * @param string Root name
     * @param boolean Is it a recursive sub-call?
     * @return void
     */
    protected function _renderNode($root = '', $subCall = false) 
    {
        if ($subCall && !empty($this->_options['useAjax'])) {
            do {
                next($this->_collection);
            } while (strpos(key($this->_collection), $root . $this->_separator) === 0);
            
            return '';
        }
        
        if ($root)
            $indent = str_repeat("\t", substr_count($root, $this->_separator) + 1);
        else
            $indent = '';
        
        $html = false;
        while ($item = current($this->_collection)) {
            $id = key($this->_collection);
            $this->_divCounter++;
            
            if ($root && strpos($id, $root) !== 0) {
                break;
            }

            if ($root)
                $idSuffix = substr($id, strlen($root) + 1);
            else
                $idSuffix = $id;
            
            $sectors = explode($this->_separator, $idSuffix);
            $idPrefix = $root . ($root ? $this->_separator : false) . $sectors[0];

            $onclick = sprintf(
                " onclick=\"$('div#tree%d').toggle()%s\"", 
                $this->_divCounter,
                (empty($this->_options['useAjax']) ? false :
                ";loadNode($('div#tree{$this->_divCounter}'), '{$idPrefix}');")
            );
            
            // is it a chapter?
            if (count($sectors) > 1) {
                if (empty($this->_options['renderSections'])) {
                    $this->_divCounter--;
                }
                $html .= sprintf(
                    "%s%s%s<div %sid=\"tree%d\" class=\"sub\">\n",
                    $indent,
                    empty($this->_options['renderSections']) ? false :
                    sprintf(
                        "<div><span%s>%s</span></div>\n",
                        $onclick,
                        empty($this->_options['suffixOnly']) ? $idPrefix : $sectors[0]
                    ),
                    $indent,
                    (empty($this->_options['startCollapsed']) ? false : "style='display:none' "),
                    $this->_divCounter
                );

                $sub = $this->_renderNode(
                    ($root ? $root . $this->_separator : false) . $sectors[0],
                    true
                );
                // something was added and we can go to the next element,
                // we're sure that NEXT() was performed inside this call
                $html .= $sub . "{$indent}</div>\n";
                if (trim($sub, "\t\n ")) {
                    continue;
                }
                // don't CONTINUE, but go below until NEXT()
            } else {
                // it's an item
                $values = array();
                foreach ($this->_tokens as $token) {
                    eval("\$value = \$item{$token};");
                    $values[] = $value;
                }

                $html .= sprintf(
                    "%s<div%s>%s</div>\n",
                    $indent,
                    !empty($this->_options['renderSections']) ? false : $onclick,
                    call_user_func_array(
                        'sprintf',
                        array_merge(
                            array($this->_mask),
                            $values
                        )
                    )
                );
            }

            next($this->_collection);
        }
        return $html;
    }

}
