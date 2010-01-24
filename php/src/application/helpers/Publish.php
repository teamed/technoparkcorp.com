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
 * Publish document on a page
 *
 * @package helpers
 */
class Helper_Publish extends FaZend_View_Helper
{

    /**
     * Document to be published
     *
     * @var Model_Artifact
     */
    protected $_doc = null;
    
    /**
     * Local ACL for publishing pages
     *
     * @var Zend_Acl
     */
    protected $_acl = null;
    
    /**
     * List of publishing pages, updated by _loadAcl()
     *
     * @var FaZend_StdObject[]
     * @see _loadAcl()
     */
    protected $_pages = array();

    /**
     * Publishes an artifact
     *
     * @param Model_Artifact The artifact to publish
     * @return Helper_Publish
     */
    public function publish(Model_Artifact $doc)
    {
        $this->_doc = $doc;

        // inject it into view, in order to allow publish pages to work with it
        $this->getView()->document = $this->_doc;
        
        $this->_loadAcl();
        return $this;
    }

    /**
     * Show in string
     *
     * @return string HTML
     */
    public function __toString() 
    {
        try {
            return $this->_render();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
        
    /**
     * Show in string
     *
     * @return string HTML
     */
    protected function _render() 
    {
        // include CSS specific for this helper
        $this->getView()->includeCSS('helper/publish.css');
        
        // current document
        $current = $this->getView()->doc;

        // define privileges of current user on current page
        $privileges = 'r';
        if (Model_Pages::getInstance()->isAllowed($current, null, 'w'))
            $privileges = 'rw';

        // build menu
        $links = array();        
        foreach ($this->_pages as $page) {            
            if ($this->_acl->isAllowed(Model_User::me()->email, $page->tag, $privileges))
                $links[$page->tag] = '<a href="' . $this->getView()->panelUrl($current) . 
                '?' . $page->tag .  '">' . $page->tag . '</a>';
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        foreach ($this->_pages as $page) {
            if (!isset($request->{$page->tag}))
                continue;

            if (isset($links[$page->tag])) {
                $pageHtml = $this->_executePage($page);
                $links[$page->tag] = '<b>' . $links[$page->tag] . '</b>';
                break;
            }

            $pageHtml = '<p class="error">You don\' have enough access ' . 
            'permissions to access this page (' . $page->tag . ')</p>';
        }
        
        // when the document was updated last time
        $age = $this->getView()->dateInterval($this->_doc->ps()->updated->get(Zend_Date::TIMESTAMP));
        
        return '<div class="publish">' .
            '<tt' . ($this->_doc instanceof Model_Artifact_Passive ? " style='color:red;'" : false) . '>' . 
                get_class($this->_doc) . '</tt>' .
            ($privileges == 'rw' ? '<sup title="you can read/write" style="cursor:pointer;"><small>rw</small></sup>: ' : false) . 
            implode('&#32;&middot;&#32;', $links) . 
            '<span style="color:gray;margin-left:20px;font-size:0.8em;">' .
                'v' . $this->_doc->ps()->version . ', updated ' . $age . ' ago' . 
                ($this->_doc instanceof Model_Artifact_Passive ? 
                    ($this->_doc->isLoaded() ? false : ' (requires reloading)') : false) . 
                '</span>' .
            '</div>' . 
            (isset($pageHtml) ? "<div class='publisher'>" . $pageHtml . '</div>' : false);
    }
    
    /**
     * Execute one page and return HTML result
     *
     * @param FaZend_StdObject Page resource
     * @return string HTML
     **/
    protected function _executePage(FaZend_StdObject $page) 
    {
        return $this->getView()->render($page->path);
    }
    
    /**
     * Loads local ACL for all possible pages for this document and user
     *
     * @return void
     **/
    protected function _loadAcl() 
    {
        $this->_acl = new Zend_Acl();
        $this->_acl->deny();

        // add current user by default
        $this->_acl->addRole(Model_User::me()->email);
        
        $prefix = 'panel/publish';
        $dir = APPLICATION_PATH . '/views/scripts/' . $prefix;
        
        foreach (glob($dir . '/*.phtml') as $file) {
            $script = pathinfo($file, PATHINFO_FILENAME);
            
            // skip system files
            if ($script[0] == '_')
                continue;
                
            $this->_pages[$script] = FaZend_StdObject::create()
                ->set('path', $prefix . '/' . $script . '.phtml')
                ->set('tag', $script);
                
            $this->_acl->addResource($script);
        }
        
        $access = $this->getView()->render($prefix . '/_access.phtml');
        $access = preg_replace('/<!--.*-->/s', '', $access);
        foreach (explode("\n", $access) as $id=>$line) {
            // ignore comments and empty lines
            if (preg_match('/^(?:\s?#.*|\s?)$/', $line))
                continue;

            if (!preg_match('/^\s?(\w+)\s?=\s?(.*)$/', trim($line, "\t\r\n "), $matches))
                FaZend_Exception::raise('Helper_Publish_InvalidSyntax', "Error in access.pthml file, line #$id: $line");
            
            if (!$this->_acl->has($matches[1]))
                $this->_acl->addResource($matches[1]);
            
            // what rights are assigned?
            switch (trim($matches[2])) {
                // no access
                case '':
                    $this->_acl->deny(null, $matches[1]);
                    break;
                
                // for reading
                case 'r':
                    $this->_acl->allow(null, $matches[1]);
                    break;
                    
                // for writing
                case 'w':
                case 'rw':
                    $this->_acl->allow(null, $matches[1], 'rw');
                    break;
                
                // email address
                default:
                    if (!$this->_acl->hasRole($matches[2]))
                        $this->_acl->addRole($matches[2]);
                    $this->_acl->allow($matches[2], $matches[1]);
                    break;
            }
        }
    }

}
