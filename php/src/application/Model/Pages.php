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
 * Collection of all pages from /pages
 *
 * We use directory /pages to build a full navigation map of pages
 * available for viewing.
 *
 * @package Model
 */
class Model_Pages extends Zend_Navigation
{

    /**
     * This constant is used in /pages scripts
     */
    const ADMIN = 'yegor256@yahoo.com';

    /**
     * Instance
     *
     * @var Model_Pages
     */
    protected static $_instance;

    /**
     * Location of all pages
     *
     * @var string
     */
    protected $_pagesPath;

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
     * Instance getter, singleton pattern
     *
     * @return Model_Pages
     */
    public static function getInstance() 
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Model_Pages();
            self::$_instance->_pagesPath = APPLICATION_PATH . '/pages';
        }
        return self::$_instance;
    }

    /**
     * Get instance of ACL
     *
     * @return Zend_Acl
     */
    public function getAcl() 
    {
        if (!isset($this->_acl))
            $this->_init();
        return $this->_acl;
    }

    /**
     * Set active document
     *
     * @param string Document name, absolute path of the page
     * @return void
     */
    public function setActiveDocument($doc) 
    {
        // the document will be activated, if it physically exists
        $this->_activateDocument($doc);

        // select active thread
        $active = $this->findBy('title', $doc);
        if ($active)
            $active->setActive();
    }

    /**
     * This page has PHTML script?
     *
     * @param string Absolute name of the page, without leading slash
     * @return boolean
     */
    public function hasScript($page) 
    {
        try {
            $path = $this->resolvePath($page);
            if (is_dir($path))
                return false;
            return true;
        } catch (Model_Pages_DocumentNotFound $e) {
            return false;
        }
    }

    /**
     * Resolve path by document name
     *
     * @param string Document name
     * @param array List of INIT scripts, will be filled
     * @return string PHTML absolute path name or directory name
     */
    public function resolvePath($doc, array &$scripts = array()) 
    {
        // all pages are located in this directory and its sub-dirs
        $path = $this->_pagesPath;

        // go through all segments of the document name
        foreach (explode('/', $doc . '.phtml') as $segment) {
            $path .= '/';

            // there is NO such path and we should try to change the
            // segment to _any or try something else
            if (!file_exists($path . $segment)) {
                // change it to _any
                $segmentReplacer = preg_replace('/^.*?(\.phtml)?$/', '_any${1}', $segment);
                // maybe it's the last segment and it's PHTML
                // but we don't have such a script, instead we have
                // a directory with this name. this condition is to
                // be considered ONLY for the last segment in a row
                if (strpos($segment, '.phtml') !== false) {
                    $segmentDir = preg_replace('/\.phtml$/', '', $segment);
                    if (file_exists($path . $segmentDir) && is_dir($path . $segmentDir)) {
                        $segmentReplacer = $segmentDir;
                    }
                }
                // replace the segment with this name (_any or name of the dir)
                $segment = $segmentReplacer;
            }

            $path .= $segment;

            if (file_exists($path . '/_init.phtml'))
                $scripts[] = $path . '/_init.phtml';
        }

        // PHTML script or directory is NOT found
        if (!file_exists($path)) {
            FaZend_Exception::raise(
                'Model_Pages_DocumentNotFound',
                "Document '{$doc}' was not found, path: '{$path}'"
            );
        }

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
    public static function resolveLink($link, $row = null, $key = null) 
    {
        // if it's empty - leave it like it is
        if (!$link)
            return $link;

        // replace meta-s, if the ROW is provided
        if (!is_null($row)) {
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

        // if the link is again empty - return it as empty
        if (!$link)
            return $link;
            
        // remove leading slash if it's an absolute path, 
        // or make it absolute from relative
        if ($link[0] == '/') {
            $link = substr($link, 1);
        } else {
            $link = self::getInstance()->findOneBy('active', true)->title . '/' . $link;
        }
        
        // Replace "/abc/.." with nothing
        $link = preg_replace('/\/[\w\d\-\.]+\/\.\./', '', $link);

        // return the document name, which can be used in panelUrl() helper
        return $link;
    }

    /**
     * This page is allowed for this particular user?
     *
     * @param string Document full name
     * @param string|null User email, NULL means current user
     * @param string Privileges to apply
     * @return boolean
     */
    public function isAllowed($doc, $email = null, $privileges = 'r') 
    {
        // the document will be activated, if it physically exists
        $this->_activateDocument($doc);

        // maybe this document is still unknown?
        if (!$this->getAcl()->has($doc)) {
            FaZend_Log::info("Document '{$doc}' doesn't exist in ACL");
            return false;
        }

        // get default user email
        if (is_null($email)) {
            $email = Model_User::me()->email;
        }

        // recursively check parent
        // DELETE IT!
        // if (strpos($doc, '/') !== false) {
        //     if (!$this->isAllowed(substr($doc, 0, strrpos($doc, '/')), $email, $privileges)) {
        //         return false;
        //     }
        // }
        
        return $this->getAcl()->isAllowed($email, $doc, $privileges);
    }

    /**
     * Build document content
     *
     * @param string Name of the document to render
     * @param array Associative array of params to pass to the view
     * @return string HTML
     */
    public function buildDocumentHtml($doc, array $params = array()) 
    {
        $view = clone $this->_view;
        $view->doc = $doc;
        
        // pass params to the view
        $view->assign($params);

        // configure it, set the active document for further references
        $this->setActiveDocument($doc);

        // convert document name into absolute PATH
        $scripts = array();
        $path = $this->resolvePath($doc, $scripts);

        $html = '';
        /**
         *  @todo this should be improved
         */
        foreach ($scripts as $script) {
            $view->addScriptPath(dirname($script));
            $html .= $view->render(pathinfo($script, PATHINFO_BASENAME));
        }

        // reconfigure VIEW in order to render this particular document file
        $view->addScriptPath(dirname($path));
        $html .= $view->render(pathinfo($path, PATHINFO_BASENAME));

        // if execution inside this view is completed - show only the result
        if ($view->formaCompleted)
            $html = '<pre class="log">' . $view->formaCompleted . '</pre>';
        return $html;
    }
    
    /**
     * Initializer
     *
     * @param Zend_Navigation_Container
     * @param string Directory to search pages for
     * @return void
     */
    protected function _init(Zend_Navigation_Container $container = null, $path = '.') 
    {
        // first level or recursion? initialize it
        if (is_null($container)) {
            $container = $this;
            $this->_acl = new Zend_Acl();
            $this->_acl->deny();
        }

        $fullPath = $this->_pagesPath . '/' . $path;

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
            if (!preg_match('/^([a-zA-Z0-9\.\@]+)\.phtml$/', $file, $matches)) {
                FaZend_Exception::raise(
                    'Model_Pages_IncorrectFormat',
                    "Line #$id has invalid format in $fullPath: '" . $file . "', .phtml file name expected"
                );
            }
                
            $container->addPage($this->_createPage($prefix . $matches[1]));
        }

        // parse _access.phtml file and build ACL
        $this->_parseAccesses($fullPath, $container, $prefix);

        // call all directories for additional pages, if they exist there
        foreach (scandir($fullPath) as $dir) {
            if ($dir[0] == '.')
                continue;
            $pagePath = $fullPath . '/' . $dir;
            
            // we need ONLY directories
            if (!is_dir($pagePath))
                continue;
                
            // try to find the page with this directory name
            $page = $container->findOneBy('label', $dir);
            
            // if the page is NOT found, maybe we need to create it?
            if (!$page) {
                // we should do this ONLY if the directory has some files inside
                if (($dir != '_any') && $this->_hasPages($pagePath)) {
                    $pageName = $prefix . $dir;
                    // create this artificial page, variable $page will be used later!
                    $page = $this->_createPage($pageName);
                    
                    // add this artificial page to the holder
                    $container->addPage($page);
                    $this->_addResource($pageName);
                } else
                    continue;
            }
            
            // initialize the sub-pages
            $this->_init($page, $path . '/' . $dir);
        }
    }
    
    /**
     * This directory has pages inside?
     *
     * @param string Absolute directory path
     * @return boolean
     */
    protected function _hasPages($path) 
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file[0] == '.')
                continue;
            return true;
        }
        return false;
    }

    /**
     * Parse PHTML file as view script
     *
     * @param string Absolute file name (PHTML file)
     * @return string Parsed content, processed through VIEW
     */
    protected function _parse($file) 
    {
        // if there is not VIEW - don't parse the file
        if (is_null($this->_view)) {
            // clone view from the system-wide object
            $this->_view = clone Zend_Registry::get('Zend_View');
            
            // disable all filters
            $this->_view->setFilter(null);
            
            // root of the entire artifact tree
            $this->_view->root = Model_Artifact::root();
            
            // add location of pages to the script path
            $this->_view->addScriptPath($this->_pagesPath);
        }

        // parse this particular file
        $content = $this->_view->render(substr($file, strlen($this->_pagesPath)));
        
        // remove PHTML comments
        $content = preg_replace('/<!--.*-->/s', '', $content);
        
        return $content;
    }

    /**
     * Parse _access.pthml file in the given folder
     *
     * @param string Folder name
     * @param Zend_Navigation_Container List of pages
     * @param string Prefix name of the page
     * @return void
     */
    public function _parseAccesses($dir, Zend_Navigation_Container $pages, $prefix) 
    {
        $accessFile = $dir . '/_access.phtml';

        // create access lines from file or leave them empty
        if (!file_exists($accessFile))
            $lines = array();
        else
            $lines = explode("\n", $this->_parse($accessFile));

        $rights = array();
        
        // current directory to apply access rights to
        $current = false;
        
        // iterate through all lines of the file
        foreach ($lines as $id=>$line) {
            $line = trim($line, "\t\n\r ");

            // skip empty lines
            if (!$line)
                continue;
                
            // skip comments
            if ($line[0] == '#')
                continue;

            // new page section, e.g. [PMO]
            if (preg_match('/^\[([\w\d]+)\]$/', $line, $matches)) {
                $current = $matches[1];
                if (!$pages->findBy('title', $prefix . $current)) {
                    FaZend_Exception::raise(
                        'Model_Pages_IncorrectFileFormat',
                        "Line #{$id}, page '{$current}' in not in the directory {$dir}: '{$line}'" .  
                        implode(', ', $pages)
                    );
                }
                continue;
            }

            // if access rights are specified - like in proper format
            if (preg_match('/^(.*?)\s?\=\s?(r|rw|)$/', $line, $matches)) {
                if ($current === false) {
                    FaZend_Exception::raise(
                        'Model_Pages_UnattachedLine',
                        "Line #{$id} in file '{$accessFile}' is not related to any page: '{$line}'"
                    );
                }

                if (!isset($rights[$current]))
                    $rights[$current] = array();
                    
                $rights[$current][$matches[1]] = ($matches[2] ? ($matches[2] == 'rw' ? 'rw' : 'r') : false);
                continue;
            }

            FaZend_Exception::raise(
                'Model_Pages_IncorrectLineFormat',
                "Line #{$id} in file '{$accessFile}' has invalid format: '{$line}' " . htmlspecialchars($line)
            );
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
    protected function _grant($email, $page, $access = 'r') 
    {
        assert(preg_match('/^r|rw|$/', $access));

        // get local copy of ACL object
        $acl = $this->getAcl();

        // create a role if it is absent
        if (($email != '*') && !$acl->hasRole($email))
            $acl->addRole($email);

        // create resource if absent
        if (!$acl->has($page))
            $this->_addResource($page);

        // allow access to everybody?
        if ($email == '*')
            $email = null;

        // if no access specified - we deny access
        if (!$access) {
            $acl->deny($email, $page);
        } else {
            if ($access == 'rw')
                $acl->allow($email, $page, 'w');
            $acl->allow($email, $page, null);
        }
    }

    /**
     * Add one page to ACL
     *
     * @param string Page name
     * @return void
     */
    protected function _addResource($page) 
    {
        $this->getAcl()->addResource(
            $page, 
            strpos($page, '/') !== false ?
            substr($page, 0, strrpos($page, '/')) : null
        );
    }

    /**
     * Find document physically and add it to the list of resources
     * 
     * @param string Full name of the document
     * @return boolean Found or not?
     */
    protected function _activateDocument($doc) 
    {
        // if it's already here - skip it
        if ($this->getAcl()->has($doc))
            return true;
            
        // we should activate the parent first, if we can
        if (strpos($doc, '/')) {
            $parent = substr($doc, 0, strrpos($doc, '/'));
            $this->_activateDocument($parent);
        }

        try {
            // here we can get an exception, if the file is not found
            $this->resolvePath($doc);
        } catch (Model_Pages_DocumentNotFound $e) {
            FaZend_Log::info($e->getMessage());
            return false;
        }

        $this->_addResource($doc);
        if (isset($parent)) {
            $parentContainer = $this->findOneBy('title', $parent);
            if (!$parentContainer) {
                FaZend_Log::info("parent not found by title '{$parent}'");
                return false;
            }
        } else {
            $parentContainer = $this;
        }

        $parentContainer->addPage($this->_createPage($doc));
        return true;
    }

    /**
     * Create a single page for container
     *
     * @param string Name of the page to be created
     * @return Zend_Navigation_Page
     */
    protected function _createPage($doc) 
    {
        return new Zend_Navigation_Page_Uri(
            array(
                'label' => (strrpos($doc, '/') ? substr(strrchr($doc, '/'), 1) : $doc),
                'title' => $doc,
                'uri' => Zend_Registry::get('Zend_View')->panelUrl($doc),
                'resource' => $doc,
            )
        );
    }

}
