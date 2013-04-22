<?php
/**
 * @version $Id$
 */

class Mocks_Model_Article_SearchProxy extends Model_Article_SearchProxy
{

    public function addArticle(Model_Article $article) 
    {
        // nothing
    }

    public function findArticles($mask) 
    {
        return array();
    }
    
    public function clean()
    {
        // nothing
    }

    public function numDocs()
    {
        return 1;
    }

    public function commit()
    {
        // nothing
    }

}
