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
 * Navigation map for static part of the site
 *
 * @package Controllers
 */
class Model_Navigation
{
    
    const USE_CACHE = true;

    /**
     * Cache of navigation map
     *
     * @var Zend_Cache
     */
    protected static $_cache;
    
    /**
     * XML text for the article, if given
     *
     * @var XMLDocument
     */
    protected $_xml;

    /**
     * Populate nagivation containter with pages
     *
     * @param Zend_Navigation
     * @param string File path with XML files
     * @return void
     */
    public static function populateNavigation(&$container, $activePage)
    {
        if (self::_cache()->test('map')) {
            // we already have this set of pages in cache
            $container->setPages(self::_cache()->load('map'));
        } else {
            // kill LUCENE search index
            Model_Article::getSearchProxy()->clean();

            // we should calculate them again
            self::_addMenuPages($container);

            // and save to cache
            self::_cache()->save($container->getPages(), 'map');
            
            logg('Indexed ' . Model_Article::getSearchProxy()->numDocs() . ' articles in Lucene');
        }

        // mark active page as "active"
        self::_markActivePage($container, $activePage);
    }

    /**
     * Add pages to menu from XML files
     *
     * The method is called recursively, i.e. will call itself for each 
     * sub-directory
     *
     * @param Zend_Navigation
     * @param string File path with XML files
     * @return void
     */
    protected static function _addMenuPages($container, $path = CONTENT_PATH, $prefix = '') 
    {
        // get full list of XML files in content directory
        foreach (glob($path . '/*.xml') as $file) {

            // get the file name, without extension
            $label = pathinfo($file, PATHINFO_FILENAME);

            // full long name of the page, like: 'about/facts'
            $fullLabel = trim($prefix . '/' . $label, '/');

            // load the article
            $article = Model_Article::createByLabel($fullLabel);
            
            // add this article to search
            // this operation takes time, but since the entire navigation-building
            // process is cached - it's OK
            Model_Article::getSearchProxy()->addArticle($article);

            // create and add new page to the current collection
            $page = new Zend_Navigation_Page_Uri(
                array(
                    'label' => $article->label,
                    'title' => $article->title,
                    'path' => $fullLabel,
                    'uri' => Zend_Registry::getInstance()->view->staticUrl($fullLabel),
                    'class' => 'l' . substr_count($fullLabel, '/'),
                )
            );

            // hide unnecessary menu items
            if (!$article->visible) {
                $page->visible = false;
            }

            // add this page to the container
            $container->addPage($page);

            // if it was a directory - add sub files to this page
            // as a sub menu
            if (is_dir($path . '/' . $label)) {
                self::_addMenuPages($page, $path . '/' . $label, $fullLabel);
            }
        }
    }

    /**
     * Find and mark active page as "active"
     *
     * @param Zend_Navigation
     * @param string Page path
     * @return void
     */
    protected static function _markActivePage($container, $activePage) 
    {
        // page label "about/facts" will be converted to ("about", "facts")
        $sections = explode('/', $activePage);

        for ($i=0; $i<count($sections); $i++) {
            $page = $container->findOneBy('path', implode('/', array_slice($sections, 0, $i+1)));
            if (is_null($page)) {
                continue;
            }

            // set it as active
            $page->active = true;
        }
    }

    /**
     * Get an instance of cache, to save the parsed Navigation map
     *
     * @return Zend_Cache
     */
    protected static function _cache() 
    {
        if (self::$_cache != false) {
            return self::$_cache;
        }

        return self::$_cache = Zend_Cache::factory(
            'Core', 
            'File', 
            array(
                'caching' => self::USE_CACHE,
                'cache_id_prefix' => 'panel2nav' . FaZend_Revision::get(),
                'lifetime' => null, // live forever
                'automatic_serialization' => true,
                'automatic_cleaning_factor' => false,
                'write_control' => true,
                'logging' => false,
                'ignore_user_abort' => true
            ), 
            array(
                'cache_dir' => TEMP_PATH,
                'hashed_directory_level' => 0,
                'read_control' => true,
                'file_locking' => true,
                'file_name_prefix' => 'panel2navigation'
            )
        );
    }

}
