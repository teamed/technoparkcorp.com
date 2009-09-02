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

    /**
     * Instance
     *
     * @var Model_Pages
     */
    protected static $_instance;

    /**
     * Access Control List
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Public constructor
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->_acl = new Zend_Acl();
        $this->_acl->deny();
        
        $this->_init($this, '.');
    }

    /**
     * Instance getter
     *
     * @param string Active document name
     * @return Model_Pages
     */
    public static function getInstance($doc = null) {
        if (!isset(self::$_instance))
            self::$_instance = new Model_Pages();

        if (!is_null($doc)) {
            $active = self::$_instance->findBy('title', $doc);
            if ($active)
                $active->setActive();
        }

        return self::$_instance;
    }

    /**
     * Resolve path by document name
     *
     * @param string Document name
     * @param array List of INIT scripts, will be filled
     * @return string PHTML absolute path name
     */
    public static function resolvePath($doc, array &$scripts = array()) {
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
     * Get instance of ACL
     *
     * @return Zend_Acl
     */
    public function getAcl() {
        return $this->_acl;
    }

    /**
     * This page is allowed for this particular user?
     *
     * @param string User email
     * @param string Document full name
     * @return boolean
     */
    public function isAllowed($email, $doc) {
        // recursively check parent
        if (strpos($doc, '/') !== false) {
            if (!$this->isAllowed($email, substr($doc, 0, strrpos($doc, '/'))))
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
    protected function _init(Zend_Navigation_Container $container, $path) {

        $fullPath = APPLICATION_PATH . '/pages/' . $path;

        if (file_exists($fullPath . '/_folders.phtml')) {
            $files = explode("\n", $this->_parse($fullPath . '/_folders.phtml'));
        } else {
            $files = scandir($fullPath);
        }

        $prefix = (($container instanceof Zend_Navigation_Page_Uri) ?
            ($container->title . '/') : false);

        // search all pages in the given directory
        foreach ($files as $file) {

            $file = trim($file, " \t\n\r");

            $matches = array();
            if (!preg_match('/^([a-zA-Z0-9\.]+)\.phtml$/', $file, $matches)) {
                continue;
            }
            $title = $matches[1];
            $doc = $prefix . $title;

            $page = new Zend_Navigation_Page_Uri(array(
                'label' => $title,
                'title' => $doc,
                'uri' => Zend_Registry::getInstance()->view->panelUrl($doc),
                'resource' => $doc,
            ));

            $container->addPage($page);
        }

        // parse _access.phtml file and build ACL
        $this->_parseAccesses($fullPath, $container, $prefix);

        // call directories
        foreach ($container->getPages() as $pg) {

            $dir = $fullPath . '/' . $pg->title;
            if (file_exists($dir) && is_dir($dir)) 
                $this->_init($pg, $path . '/' . $pg->title);

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
        $view = new Zend_View();
        $view->setScriptPath(dirname($file));
        return $view->render(basename($file));
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

        // create a role if it is absent
        if (($email != '*') && !$this->_acl->hasRole($email))
            $this->_acl->addRole($email);

        // create resource if absent
        if (!$this->_acl->has($page))
            $this->_addResource($page);

        if ($email == '*')
            $email = null;

        if (!$access)
            $this->_acl->deny($email, $page);
        else
            // allow access for this actor to this resource
            $this->_acl->allow($email, $page/*str_split($access)*/);

    }

    /**
     * Add one page to ACL
     *
     * @param string Page name
     * @return void
     */
    protected function _addResource($page) {
        $this->_acl->addResource($page, strpos($page, '/') !== false ?
            substr($page, 0, strrpos($page, '/')) : null);
    }


}
