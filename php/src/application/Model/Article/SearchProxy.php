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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Proxy to search engine
 *
 * @package Controllers
 */
class Model_Article_SearchProxy
{

    /**
     * Lucene search instance
     *
     * @var Zend_Search_Lucene
     */
    protected $_lucene = null;
    
    /**
     * Directory name of the lucene storag
     *
     * @var string
     **/
    protected $_lucenePath = null;

    /**
     * Clean entire index
     *
     * @return void
     */
    public function clean() 
    {
        $this->lucene(true);
    }

    /**
     * Total number of docs?
     *
     * @return integer
     */
    public function numDocs() 
    {
        return $this->lucene()->numDocs();
    }

    /**
     * Add new article to index
     *
     * @param Model_Article
     * @return void
     */
    public function addArticle(Model_Article $article) 
    {
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('page', $article->page));
        foreach (array('label', 'title', 'description', 'keywords', 'text') as $field)
            $doc->addField(Zend_Search_Lucene_Field::UnStored($field, $article->$field));
        $this->lucene()->addDocument($doc);
        $this->lucene()->commit();
    }
    
    /**
     * Find articles by given mask
     *
     * @return Model_Article[]
     */
    public function findArticles($mask) 
    {
        $articles = array();
        foreach ($this->lucene()->find($mask) as $hit) {
            $doc = $hit->getDocument();
            $article = Model_Article::createByLabel($doc->page);
        }
        return $articles;
    }

    /**
     * Get directory name of the LUCENE storage
     *
     * @return string
     **/
    public function getLucenePath() 
    {
        if (is_null($this->_lucenePath))
            $this->setLucenePath(TEMP_PATH . '/panel2lucene');
        return $this->_lucenePath;
    }

    /**
     * Set directory name of the LUCENE storage
     *
     * @param string Directory path
     * @return void
     * @throws Model_Article_InvalidLucenePath
     **/
    public function setLucenePath($path) 
    {
        // it is absent? we should create it
        if (!file_exists($path)) {
            if (!@mkdir($path)) {
                FaZend_Exception::raise(
                    'Model_Article_InvalidLucenePath',
                    "Dir specified '{$path}' is absent and can't be created"
                );
            }
        }

        // it's not a directory?
        if (!is_dir($path)) {
            FaZend_Exception::raise(
                'Model_Article_InvalidLucenePath',
                "Path specified '{$path}' is not a directory"
            );
        }
        
        // it's not writable? permissions problem?
        if (!is_writable($path)) {
            FaZend_Exception::raise(
                'Model_Article_InvalidLucenePath',
                "Path specified '{$path}' is not writable"
            );
        }
        
        $this->_lucenePath = $path;
    }

    /**
     * Return search engine instance
     *
     * @param boolean Shall we kill the existing index and start over?
     * @return Zend_Search_Lucene_Proxy
     */
    public function lucene($refresh = false) 
    {
        if (!isset($this->_lucene) || $refresh) {        
            $path = $this->getLucenePath();
            
            if (file_exists($path) && is_dir($path) && !$refresh)
                $this->_lucene = Zend_Search_Lucene::open($path);
            else
                $this->_lucene = Zend_Search_Lucene::create($path);
                
            Zend_Search_Lucene::setResultSetLimit(20);
            Zend_Search_Lucene::setTermsPerQueryLimit(100);
        }
        
        return $this->_lucene;
    }

}
