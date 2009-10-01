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
 * Collection of all pages from /pages
 *
 * We use directory /pages to build a full navigation map of pages
 * available for viewing.
 *
 * @package Model
 */
class Model_Pages extends Zend_Navigation {

    const ADMIN = 'director@tpc2.com';

    /**
     * Instance
     *
     * @var Model_Pages
     */
    protected static $_instance;

    /**
     * The view to use when parsing PTHML special files
     *
     * @var Zend_View
     */
    protected $_view = null;

    /**
     * Access Control List
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Instance getter
     *
     * @return Model_Pages
     */
    public static function getInstance() {
        if (!isset(self::$_instance))
            self::$_instance = new Model_Pages();

        return self::$_instance;
    }

    /**
     * Set the View
     *
     * @param Zend_View View to use
     * @return void
     */
    public function setView(Zend_View $view) {
        // save it locally, but a clone, in order to avoid conflicts
        // with the main execution stream
        $this->_view = clone $view;
    }

    /**
     * Get instance of ACL
     *
     * @return Zend_Acl
     */
    public function getAcl() {
        if (!isset($this->_acl))
            $this->_init();
        return $this->_acl;
    }

    /**
     * Set active document
     *
     * @param string Document name
     * @return void
     */
    public function setActiveDocument($doc) {

        // the document will be activated, if it physically exists
        $this->_activateDocument($doc);

        // select active thread
        $active = $this->findBy('title', $doc);
        if ($active)
            $active->setActive();
    }

    /**
     * Resolve path by document name
     *
     * @param string Document name
     * @param array List of INIT scripts, will be filled
     * @return string PHTML absolute path name
     */
    public function resolvePath($doc, array &$scripts = array()) {
        $path = APPLICATION_PATH . '/pages';

        foreach (explode('/', $doc . '.phtml') as $segment) {
            $path .= '/';

            if (!file_exists($path . $segment))
                $segment = preg_replace('/^.*?(\.phtml)?$/', '_any${1}', $segment);

            $path .= $segment;

            if (file_exists($path . '/_init.phtml'))
                $scripts[] = $path . '/_init.phtml';
        }

        if (!file_exists($path))
            FaZend_Exception::raise('Model_Pages_DocumentNotFound',
                "Document $doc not found, path: $path");

        return $path;
    }

    /**
     * Resolve link from string and array/object
     *
     * You can call this method to resolve the link using some object. For
     * example:
     *
     * <code>
     * $object = new Activity(222);
     * $link = 'project/{project}/schedule/{id}';
     * echo Model_Pages::resolveLink($link, $object);
     * </code>
     *
     * Metas 'project' and 'id' will be replaced by values from $object
     *
     * @param string Link with meta-symbols, like "wobots/{name}/details"
     * @param array|object Source of data for resolving the link metas
     * @return string
     */
    public static function resolveLink($link, $row = null, $key = null) {
        if (!$link)
            return $link;

        // replace meta-s
        if (!is_null($row)) {
            $matches = array();
            if (preg_match_all('/\{(.*?)\}/', $link, $matches)) {
                foreach ($matches[0] as $id=>$match) {
                    $name = $matches[1][$id];
                    if ($name == '__key') {
                        $value = $key;
                    } else if (is_array($row)) {
                        $value = $row[$name];
                    } else {
                        if (method_exists($row, $name))
                            $value = $row->$name();
                        else
                            $value = $row->$name;
                    }
                    $link = str_replace($match, $value, $link);
                }
            }

        }

        if (!$link)
            return $link;
            
        if ($link[0] == '/')
            $link = substr($link, 1);
        else
            $link = self::getInstance()->findOneBy('active', true)->title . '/' . $link;

        return $link;
    }

    /**
     * This page is allowed for this particular user?
     *
     * @param string Document full name
     * @param string|null User email, NULL means current user
     * @param string|null Privileges to apply
     * @return boolean
     */
    public function isAllowed($doc, $email = null, $privileges = null) {
        // the document will be activated, if it physically exists
        $this->_activateDocument($doc);

        if (!$this->getAcl()->has($doc))
            return false;

        // get default user email
        if (is_null($email))
            $email = Model_User::me()->email;

        // recursively check parent
        if (strpos($doc, '/') !== false) {
            if (!$this->isAllowed(substr($doc, 0, strrpos($doc, '/')), $email, $privileges))
                return false;
        }
        
        return $this->getAcl()->isAllowed($email, $doc);
    }

    /**
     * Initializer
     *
     * @param Zend_Navigation_Container
     * @param string Directory to search pages for
     * @return void
     */
    protected function _init(Zend_Navigation_Container $container = null, $path = '.') {

        // first level or recursion? initialize it
        if (is_null($container)) {
            $container = $this;
            $this->_acl = new Zend_Acl();
            $this->_acl->deny();
        }

        $fullPath = APPLICATION_PATH . '/pages/' . $path;

        if (file_exists($fullPath . '/_folders.phtml')) {
            $files = explode("\n", $this->_parse($fullPath . '/_folders.phtml'));
        } else {
            $files = scandir($fullPath);
        }

        $prefix = (($container instanceof Zend_Navigation_Page_Uri) ?
            ($container->title . '/') : false);

        // search all pages in the given directory
        foreach ($files as $id=>$file) {

            $file = trim($file, " \t\n\r");

            // ignore empty lines
            if (!$file)
                continue;
                
            // ignore dirs and special files
            if (($file[0] == '.') || ($file[0] == '_'))
                continue;

            // ignore directories
            if (file_exists($fullPath . '/' . $file) && is_dir($fullPath . '/' . $file))
                continue;

            // notify about unknown format
            $matches = array();
            if (!preg_match('/^([a-zA-Z0-9\.]+)\.phtml$/', $file, $matches))
                FaZend_Exception::raise('Model_Pages_IncorrectFormat',
                    "File $id has invalid format in $fullPath: '" . $file . "'");
                
            $container->addPage($this->_createPage($prefix . $matches[1]));
        }

        // parse _access.phtml file and build ACL
        $this->_parseAccesses($fullPath, $container, $prefix);

        // call directories
        foreach ($container->getPages() as $pg) {

            $dir = $fullPath . '/' . $pg->label;
            if (file_exists($dir) && is_dir($dir)) 
                $this->_init($pg, $path . '/' . $pg->label);

            $anyDir = $fullPath . '/_any';
            if (file_exists($anyDir) && is_dir($anyDir))
                $this->_init($pg, $path . '/_any');

        }

    }

    /**
     * Parse file as view script
     *
     * @param string Absolute file name
     * @return string
     */
    protected function _parse($file) {

        // if there is not VIEW - don't parse the file
        if (is_null($this->_view))
            return file_get_contents($file);

        // parse this particular file
        $this->_view->setScriptPath(dirname($file));
        $parsed = $this->_view->render(basename($file));
        return $parsed;

    }

    /**
     * Parse _access.pthml file in the given folder
     *
     * @param string Folder name
     * @param Zend_Navigation_Container List of pages
     * @param string Prefix name of the page
     * @return void
     */
    public function _parseAccesses($dir, Zend_Navigation_Container $pages, $prefix) {
        $accessFile = $dir . '/_access.phtml';

        if (!file_exists($accessFile))
            $lines = array();
        else
            $lines = explode("\n", $this->_parse($accessFile));

        $rights = array();

        $current = false;
        foreach ($lines as $id=>$line) {
            $line = trim($line, "\t\n\r ");

            if (!$line)
                continue;

            $matches = array();
            if (preg_match('/^\[(.*?)\]$/', $line, $matches)) {
                $current = $matches[1];

                if (!$pages->findBy('title', $prefix . $current))
                    FaZend_Exception::raise('Model_Pages_IncorrectFileFormat',
                        "Line $id, page $current in not in the directory $dir: $line" .  implode(', ', $pages));

                continue;
            }

            $matches = array();
            if (preg_match('/^(.*?)\s?\=\s?(r|rw|)$/', $line, $matches)) {

                if (!$current)
                    FaZend_Exception::raise('Model_Pages_IncorrectFileFormat',
                        "Line $id in file $accessFile is not related to any page: $line");

                if (!isset($rights[$current]))
                    $rights[$current] = array();
                $rights[$current][$matches[1]] = $matches[2];
                continue;
            }

            FaZend_Exception::raise('Model_Pages_IncorrectFileFormat',
                "Line $id in file $accessFile has invalid format: $line");
        }

        // create resources
        foreach ($pages->getPages() as $pg) {
            if (!isset($rights[$pg->name]))
                $this->_addResource($pg->title);
        }

        // move rights to ACL
        foreach ($rights as $page=>$users) {
            foreach ($users as $email=>$access)
                $this->_grant($email, $prefix . $page, $access);
        }
    }

    /**
     * Grant access to the resource for a given user
     *
     * @param string Name of the user or *
     * @param string Name of resource (page)
     * @param string Access type (false|r|rw|w)
     * @return void
     */
    protected function _grant($email, $page, $access = 'r') {

        assert(preg_match('/^r|rw|$/', $access));

        $acl = $this->getAcl();

        // create a role if it is absent
        if (($email != '*') && !$acl->hasRole($email))
            $acl->addRole($email);

        // create resource if absent
        if (!$acl->has($page))
            $this->_addResource($page);

        if ($email == '*')
            $email = null;

        if (!$access)
            $acl->deny($email, $page);
        else
            // allow access for this actor to this resource
            $acl->allow($email, $page/*str_split($access)*/);

    }

    /**
     * Add one page to ACL
     *
     * @param string Page name
     * @return void
     */
    protected function _addResource($page) {
        $this->getAcl()->addResource($page, strpos($page, '/') !== false ?
            substr($page, 0, strrpos($page, '/')) : null);
    }

    /**
     * Find document physically and add it to the list of resources
     * 
     * @param string Full name of the document
     * @return boolean Found or not?
     */
    protected function _activateDocument($doc) {

        // if it's already here - skip it
        if ($this->getAcl()->has($doc))
            return true;
            
        // we should active the parent first, if we can
        if (strpos($doc, '/')) {
            if (!$this->_activateDocument($parent = substr($doc, 0, strrpos($doc, '/'))))
                return false;
        }

        try {
            // here we can get an exception, if the file is not found
            $path = $this->resolvePath($doc);
        } catch (Model_Pages_DocumentNotFound $e) {
            return false;
        }

        $this->_addResource($doc);
        $parentContainer = isset($parent) ? $this->findOneBy('title', $parent) : $this;

        $parentContainer->addPage($this->_createPage($doc));

    }

    /**
     * Create a single page for container
     *
     * @param string Name of the page to be created
     * @return Zend_Navigation_Page
     */
    protected function _createPage($doc) {
        return new Zend_Navigation_Page_Uri(array(
            'label' => (strrpos($doc, '/') ? substr(strrchr($doc, '/'), 1) : $doc),
            'title' => $doc,
            'uri' => Zend_Registry::getInstance()->view->panelUrl($doc),
            'resource' => $doc,
        ));
    }

}
