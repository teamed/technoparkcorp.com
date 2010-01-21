<?php

require_once 'AbstractTest.php';

class ArticleTest extends AbstractTest 
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