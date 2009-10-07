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
 * Static article, created by a label
 *
 * Properties, that can be accessed as variables:
 *    - xml:               Model_XML
 *    - page:              location of the page, like "process/scope/srs"
 *    - title:             title of the article
 *    - charset:           real charset set in XML file or "UTF-8"
 *    - description:       article description
 *    - keywords:          list of keywords, coma-separated
 *    - label:             short label of the article, for the menu
 *    - intro:             intro for the right column
 *    - visible:           shall we put this article to the menu?
 *    - showRightColumn:   shall we show the right column at all?
 *    - published:         when the article was published or FALSE
 *    - term:              term to be referenced to this article
 *    - concepts:          array or objects with links to key concepts related
 *    - steps:             array of objects giving information about articles to show next to it
 *
 * @package Controllers
 */
class Model_Article {

    /**
     * XML text for the article, if given
     *
     * @var XMLDocument
     */
    protected $_xml;

    /**
     * Current page to view
     *
     * @var string
     */
    protected $_page;

    /**
     * Local cached values, to avoid double calculation
     *
     * @var string[]
     */
    protected $_cache = array();

    /**
     * Lucene search instance
     *
     * @var Zend_Search_Lucene
     */
    protected static $_lucene;

    /**
     * Create new article
     *
     * @return void
     * @throw Model_Article_NotFound
     */
    protected function __construct() {
    }

    /**
     * Create new article by file
     *
     * @param string XML file path
     * @return void
     * @throw Model_Article_NotFound
     */
    public static function createFromFile($file, $page) {

        $article = new Model_Article();

        $article->_page = $page;
        $article->_xml = Model_XML::loadFile($file);

        return $article;

    }

    /**
     * Create new article by label
     *
     * @return void
     * @throw Model_Article_NotFound
     */
    public static function createByLabel($page) {

        $xmlFile = '/' . $page;

        if (!file_exists(CONTENT_PATH . '/' . $xmlFile . '.xml')) {
            $xmlFile .= '/intro'; 

            // if it's absent - we go away
            if (!file_exists(CONTENT_PATH . '/' . $xmlFile . '.xml'))
                FaZend_Exception::raise('Model_Article_NotFound', 
                    "page $page not found: (tried {$xmlFile}.xml)");
        }    

        return self::createFromFile(CONTENT_PATH . '/' . $xmlFile . '.xml', $page);

        // get content from XML file    
        /*
        if (isset($this->_xml->text)) {
            $this->view->content = $this->_xml->text->asClearXML();
            if ($this->_xml->text['class'] == 'tex') {
                $this->view->content = Model_TeX2HTML::convert($this->view->content);
            }
        } else {
        }
        */   

    }
    
    /**
     * Return search engine instance
     *
     * @return Zend_Search_Lucene
     */
    public static function lucene() {
        
        if (!isset(self::$_lucene)) {        
            $path = TEMP_PATH . '/panel2lucene';
            if (file_exists($path))
                $index = Zend_Search_Lucene::create($path);
            else
                $index = Zend_Search_Lucene::open($path);
        }
        
        return self::$_lucene;
        
    }

    /**
     * Add this article to lucene search index
     *
     * @return void
     */
    public function luceneIndex() {
        $hits = self::lucene()->find('path:' . $this->path);
        if (count($hits) > 1) {
            foreach ($hits as $hit)
                self::lucene()->delete($hit->id);
        }
        
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $this->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('path', $this->path));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('content', $this->text));
        self::lucene()->addDocument($doc);
    }

    /**
     * Parse calls to key methods
     *
     * @param string Name of the key, mapped to _$key method, like: $this->title ---> $this->getTitle()
     * @return string
     */
    public function __get($key) {

        // maybe we already calculated it before?
        if (isset($this->_cache[$key]))
            return $this->_cache[$key];

        $method = '_get' . ucfirst($key);

        // calculate now and save to cache
        if (method_exists($this, $method))
            return $this->_cache[$key] = $this->$method();

        $key = '_' . $key;

        if (!property_exists($this, $key))
            return $key . ' is absent';

        return $this->$key;
    }

    /**
     * Returns formated time of page update
     *
     * @return string
     */
    protected function _getUpdated() {
        if (file_exists(CONTENT_PATH . '/' . $this->_page . '.xml'))
            return date('d-M-y', filemtime(CONTENT_PATH . '/' . $this->_page . '.xml'));

        return date('d-M-y', time());    
    }

    /**
     * Get article label
     *
     * @return string
     */
    protected function _getText() {
        if ($this->_xml->text)
            return (string)$this->_xml->text;

        return false;
    }

    /**
     * Get article label
     *
     * @return string
     */
    protected function _getLabel() {
        if (!$this->_xml->label)
            return $this->page;

        return trim((string)$this->_xml->label, "\n\t ");
    }

    /**
     * Get article visibility in menu
     *
     * @return boolean
     */
    protected function _getVisible() {
        if ($this->_xml->invisible)
            return false;

        return true;
    }

    /**
     * Returns the HTML Title of the page
     *
     * @return string
     */
    protected function _getTitle() {
        return trim(ucwords((string)$this->_xml->title));
    }    

    /**
     * Get the HTML keywords for the article
     *
     * The function builds the keywords from the text of the article.
     * If the keywords are defined in the special keywords.xml file - the
     * function uses this file. Also, if the article/keywords is defined,
     * it is returned
     *
     * @return string|false
     */
    protected function _getKeywords() {

        if ($this->_xml->keywords)
            return ucwords(trim((string)$this->_xml->keywords));

        // Remove spaces and other un-readable symbols
        $txt = ucwords(preg_replace('/(\s*[^a-z0-9A-Z]\s*)/', ' ', strip_tags((string)$this->_xml->text)));
        
        // Filter the words that are longer than 3 symbols and counts them
        $words = array_count_values(array_filter(explode(' ', $txt), create_function('$word', 
            'return(strlen($word) > 3) && ((int) $word[0] == 0);')));

        // Sort in reverse mode(top elements are the most popular words)
        arsort($words);

        // Move words back to value(they are in indexes now)
        array_walk($words, create_function('&$item, $key', '$item = $key;'));

        // Get the top 20 of them
        return substr(strrchr($this->page, '/'), 1).', '.implode(', ', array_slice($words, 0, 20));
    }    

    /**
     * Returns the HTML description for the page
     *
     * @return string|false
     */
    protected function _getDescription() {

        if ($this->_xml->description)
            return trim(preg_replace("/[\t\n\r]+/", ' ', (string)$this->_xml->description));

        // Remove all unreadable symbols and cut the line to 500 symbols
        if ($this->_xml->text)
            return cutLongLine(preg_replace('/(\s*[^a-z0-9A-Z\-\.\,]\s*)/', ' ',
                trim(strip_tags((string)$this->_xml->text), "\t\n ")), 500);

        // no text, no description
        return '...';

    }    

    /**
     * Charset
     *
     * @return string
     */
    protected function _getCharset() {

        if ($this->_xml->charset)
            return (string)$this->_xml->charset;

        return 'UTF-8';    

    }    

    /**
     * Show right column or not?
     *
     * @return boolean
     */
    protected function _getShowRightColumn() {
        
        if ($this->_xml->hideRightColumn)
            return false;

        return true;

    }

    /**
     * When the article was published
     *
     * @return string
     */
    protected function _getPublished() {
        
        if ($this->_xml->date)
            return (string)$this->_xml->date;

        return false;

    }

    /**
     * Get INTRO text
     *
     * @return string
     */
    protected function _getIntro() {
        
        if (!$this->_xml->intro)
            return $this->title;

        return (string)$this->_xml->intro;

    }

    /**
     * List of concepts on the bottom
     *
     * @return stdObject[]
     */
    protected function _getConcepts() {
        
        if (!$this->_xml->concepts)
            return false;

        $result = array();
        foreach ($this->_xml->concepts->children() as $concept) {
            $cls = new FaZend_StdObject();
            $cls->page = (string)$concept->attributes()->name;
            $cls->title = (string)$concept;
            $result[] = $cls;
        }

        return $result;

    }

    /**
     * Term, if it's set
     *
     * @return string
     */
    protected function _getTerm() {
        
        if (!$this->_xml->term)
            return false;

        $term = trim((string)$this->_xml->term, "\n\t ");
        $terms = Model_XML::loadFile(CONTENT_PATH . '/terms.xml');

        if (!$terms->$term)
            return $term . ' is missed';

        return (string)$terms->$term;

    }

    /**
     * List of steps to follow on the right column
     *
     * @param integer Maximum amount of steps to get
     * @return stdObject[]
     */
    protected function _getSteps($maximum = 3) {

        $next = (string)$this->_xml->next;
        if (!$next || ($maximum < 1))
            return array($this->_createStep(null));

        $article = Model_Article::createByLabel($next);
        $steps = $article->_getSteps($maximum - 1);

        // if we already have this step - skip the adding process
        foreach ($steps as $existing)
            if ($existing->page === $article->page)
                return $steps;

        // add this new step ON TOP of the list
        return array_merge(array($this->_createStep($article->page, $article->intro)), $steps);

    }

    /**
     * Create step object
     *
     * @param string Page path (if null - return default "stop step"
     * @param string Title
     * @return stdObject
     */
    protected function _createStep($page = null, $title = null) {

        $step = new FaZend_StdObject();

        if (!is_null($page)) {
            $step->page = $page;
            $step->title = $title;
        } else {
            $step->page = 'contacts/order';
            $step->title = Model_Article::createByLabel($step->page)->intro;
        }

        return $step;

    }

}
