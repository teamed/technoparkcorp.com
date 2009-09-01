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
     * Public constructor
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();

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
     * @return string
     */
    public static function resolvePath($doc) {
        $path = APPLICATION_PATH . '/pages';

        foreach (explode('/', $doc . '.phtml') as $segment) {
            $path .= '/';

            if (!file_exists($path . $segment))
                $segment = preg_replace('/^.*?(\..*?)?$/', '_any${1}', $segment);

            $path .= $segment;
        }

        if (!file_exists($path))
            FaZend_Exception::raise('Model_Pages_DocumentNotFound',
                "Document $doc not found, path: $path");

        return $path;
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

        // search all pages in the given directory
        foreach ($files as $file) {

            $file = trim($file, " \t\n\r");

            $matches = array();
            if (!preg_match('/^([a-zA-Z0-9]+)\.phtml$/', $file, $matches)) {
                continue;
            }
            $title = $matches[1];
            $doc = (($container instanceof Zend_Navigation_Page_Uri) ?
                ($container->title . '/') : false) . $title;

            $page = new Zend_Navigation_Page_Uri(array(
                'label' => $title,
                'title' => $doc,
                'uri' => Zend_Registry::getInstance()->view->panelUrl($doc),
            ));

            $container->addPage($page);

            $dir = $fullPath . '/' . $title;
            if (file_exists($dir) && is_dir($dir)) 
                $this->_init($page, $path . '/' . $title);

            $anyDir = $fullPath . '/_any';
            if (file_exists($anyDir) && is_dir($anyDir))
                $this->_init($page, $path . '/_any');

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

}
