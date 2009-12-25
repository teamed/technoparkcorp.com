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

require_once 'FaZend/Test/TestCase.php';

/**
 * Model_Article test
 *
 * @package test
 */
class ArticleTest extends FaZend_Test_TestCase 
{

    public function testArticleCanBeCreatedByLabel() 
    {
        $article = Model_Article::createByLabel('process/cost');
        $this->assertTrue(
            $article instanceof Model_Article, 
            'Article was not created, why?'
            );
    }

    public function testArticleAttributesAreAccessible() 
    {
        $article = Model_Article::createByLabel('process/cost');

        $this->assertTrue(
            $article->updated instanceof Zend_Date, 
            'Model_Article::updated returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->text), 
            'Model_Article::text returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->label), 
            'Model_Article::label returned something strange, why?'
            );

        $this->assertTrue(
            is_bool($article->visible), 
            'Model_Article::visible returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->title), 
            'Model_Article::title returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->keywords), 
            'Model_Article::keywords returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->description), 
            'Model_Article::description returned something strange, why?'
            );

        $this->assertTrue(
            ($article->published instanceof Zend_Date) || ($article->published === false), 
            'Model_Article::published returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->intro), 
            'Model_Article::intro returned something strange, why?'
            );

        $this->assertTrue(
            is_array($article->concepts) || ($article->concepts === false), 
            'Model_Article::concepts returned something strange, why?'
            );

        $this->assertTrue(
            is_string($article->term) || ($article->term === false), 
            'Model_Article::term returned something strange, why?'
            );

        $this->assertTrue(
            is_array($article->steps), 
            'Model_Article::steps returned something strange, why?'
            );
    }

    public function testLuceneIndexationWorks() 
    {
        $lucene = Model_Article::lucene(true);
        $this->assertTrue(
            $lucene instanceof Zend_Search_Lucene_Proxy, 
            'Lucene index was not created, why?'
            );
 
        $article = Model_Article::createByLabel('process/cost');
        $article->luceneIndex();
    }
    
}