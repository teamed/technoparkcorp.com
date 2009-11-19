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
     * List of pages that are ignored from Trac (they are system pages)
     *
     * Trac 0.11 is used
     *
     * @var array
     */
    protected static $_ignoredPages = array(
        'WikiNewPage',
        'TracModWSGI', 
        'TracInstall', 
        'WikiRestructuredText', 
        'TracInterfaceCustomization', 
        'WikiMacros', 
        'TracImport', 
        'TracPermissions', 
        'TracCgi', 
        'InterMapTxt', 
        'TracUpgrade', 
        'TracTickets', 
        'TracChangeset', 
        'TracSyntaxColoring', 
        'InterWiki', 
        'WikiHtml', 
        'TracIni', 
        'WikiStart', 
        'TracNavigation', 
        'WikiFormatting', 
        'TracEnvironment', 
        'TracNotification', 
        'TracWorkflow', 
        'SandBox', 
        'TracLogging', 
        'TracQuery', 
        'TracSearch', 
        'TracModPython', 
        'TracRevisionLog', 
        'TracAccessibility', 
        'WikiRestructuredTextLinks', 
        'TracWiki', 
        'TracUnicode', 
        'InterTrac', 
        'TracTimeline', 
        'TitleIndex', 
        'TracBrowser', 
        'TracFineGrainedPermissions', 
        'PageTemplates', 
        'TracTicketsCustomFields', 
        'TracRss', 
        'TracBackup', 
        'TracReports', 
        'WikiPageNames', 
        'TracAdmin', 
        'RecentChanges', 
        'TracGuide', 
        'WikiProcessors', 
        'TracPlugins', 
        'CamelCase', 
        'TracSupport', 
        'ActorUser', 
        'TracRoadmap', 
        'TracStandalone', 
        'WikiDeletePage', 
        'TracLinks', 
        'TracFastCgi');

    /**
     * The project related to this Trac
     *
     * @var Model_Project
     */
    protected $_project;
    
    /**
     * List of entities found
     *
     * @var Model_Wiki_Entity_Abstract''
     */
    protected $_entities;

    /**
     * List of all pages in wiki
     *
     * Key of array is the name of page, value is either TRUE (already visited) or
     * FALSE, not yet visited.
     *
     * @var string''
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
     * @return Model_Wiki_Entity_Abstract''
     **/
    public function retrieveAll() {
        $this->_pages = array_fill_keys($this->getXmlRpcWikiProxy()->getAllPages(), false);
        $this->_parsePage('SRS');
        
        // parse everything else, which was NOT referred from SRS
        foreach ($this->_pages as $page=>$parsed) {
            // already parsed?
            if ($parsed)
                continue;
                
            // this page should be ignored, it's system page
            if (in_array($page, self::$_ignoredPages))
                continue;
            
            $this->_parsePage($page);
        }
        
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
     * @param string Name of the page
     * @return void
     **/
    protected function _parsePage($page) {
        // page is absent in WIKI
        if (!isset($this->_pages[$page]))
            return;
            
        // the page already visited and parsed?
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
            
        preg_match_all('/<p>\s*<a\s+href=\"' . preg_quote($this->_getWikiUrl(), '/') . 
            '([\w\d\.\-]+)\">([\w\d\.]+)\??<\/a>\s*(?:\[([\w\,]+)\])?:\s?(.*?)\s*<\/p>/', $html, $matches);
        
        foreach ($matches[2] as $id=>$name) {
            // it's not an entity
            if (!Model_Wiki_Entity_Abstract::isEntity($name))
                continue;
                
            // already here
            if (isset($this->_entities[$name]))
                continue;
                
            // create a new entity and add to the list
            $this->_entities[$name] = new Model_Wiki_Entity_Trac($name, $filterTagsOut->filter($matches[4][$id]));
            
            // attribs specified?
            if ($matches[3][$id]) {
                $attribs = explode(',', $matches[3][$id]);
                foreach ($attribs as $attrib)
                    $this->_entities[$name]->setAttribute(strtolower(trim($attrib)));
            }
            
            // parse page which is referred by this entity
            $this->_parsePage($matches[1][$id]);
        }
        
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
