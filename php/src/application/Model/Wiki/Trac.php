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
 * Wiki storage in Trac
 *
 * @package Model
 */
class Model_Wiki_Trac extends Model_Wiki_Abstract {

    const URI = 'http://trac.fazend.com';

    /**
     * The project related to this Trac
     *
     * @var Model_Project
     */
    protected $_project;
    
    /**
     * List of entities found
     *
     * @var Model_Wiki_Entity_Abstract[]
     */
    protected $_entities;

    /**
     * List of all pages in wiki
     *
     * Key of array is the name of page, value is either TRUE (already visited) or
     * FALSE, not yet visited.
     *
     * @var string[]
     */
    protected $_pages;

    /**
	 * Construct the class
     *
     * @param Model_Project The project, owner of this trac
     * @return void
     */
	public function __construct(Model_Project $project) {
	    $this->_project = $project;
	    $this->_entities = new ArrayIterator();
	}

    /**
     * Retrieve all wiki entities
     *
     * @return Model_Wiki_Entity_Abstract[]
     **/
    public function retrieveAll() {
        $this->_pages = array_fill_keys($this->getXmlRpcWikiProxy()->getAllPages(), false);
        $this->_parsePage('SRS');
        return $this->_entities;
    }
    
    /**
     * Get XML RPC client instance
     *
     * @return Zend_XmlRpc_Client
     **/
    public function getXmlRpcWikiProxy() {
        // this is the URL of trac hack XMLRPC
        return Model_Client_Rpc::factory(
            $this->_project, 
            self::URI . '/' . $this->_project->name . '/xmlrpc', 
            'wiki');
    }

    /**
     * Parse one page and find all entities in it
     *
     * @return void
     **/
    protected function _parsePage($page) {
        // page is absent in WIKI
        if (!isset($this->_pages[$page]))
            return;
            
        // the page already visited
        if ($this->_pages[$page] === true)
            return;
            
        // mark this page as visited
        $this->_pages[$page] = true;
        
        $filterHtml = new FaZend_View_Filter_HtmlCompressor();
        $filterStripTags = new Zend_Filter_StripTags(array(
            'allowTags' => array('a', 'p'),
            'allowAttribs' => array('href')
            ));
        $filterTagsOut = new Zend_Filter_StripTags();

        $html = $filterStripTags->filter($filterHtml->filter($this->getXmlRpcWikiProxy()->getPageHTML($page)));
            
        preg_match_all('/<p><a\shref=\"' . preg_quote($this->_getWikiUrl(), '/') . 
            '([\w\d\.\-]+)\">([\w\d\.]+)\??<\/a>(?:\[([\w\,]+)\])?:\s?(.*?)<\/p>/', $html, $matches);
        
        foreach ($matches[2] as $id=>$name) {
            // it's not an entity
            if (!$this->_isEntityName($name))
                continue;
            // already here
            if (isset($this->_entities[$name]))
                continue;
            $this->_entities[$name] = Model_Flyweight::factory(
                'Model_Wiki_Entity_Trac', 
                $name, 
                $filterTagsOut->filter($matches[4][$id]));
            
            // attribs specified?
            if ($matches[3][$id]) {
                $attribs = explode(',', $matches[3][$id]);
                foreach ($attribs as $attrib)
                    $this->_entities[$name]->attributes->set($attrib);
            }
            
            $this->_parsePage($matches[1][$id]);
        }
        
    }

    /**
     * Is it a name of entity?
     *
     * @return boolean
     **/
    protected function _isEntityName($name) {
        return preg_match('/^(R|QOS)\d+(\.\d+)*|(If|Actor)[A-Z]\w+$/', $name);
    }

    /**
     * Wiki url prefix
     *
     * @return string
     **/
    protected function _getWikiUrl() {
        return self::URI . '/' . $this->_project->name . '/wiki/';
    }

}
