<?php
/**
 * @version $Id$
 */

class Injector extends FaZend_Test_Injector 
{
    
    protected function _injectTestLogger() 
    {
        // log errors in ALL environments
        // ...
        
        // keep it on top of everything else!
        $this->_bootstrap('db');
    }
    
    protected function _injectMiscellaneous() 
    {
        // just to try the translation
        $this->_bootstrap('fz_translate');
        Zend_Registry::get('Zend_Translate')->setLocale(new Zend_Locale('ru'));
    }
    
    protected function _injectSearchProxy() 
    {
        Model_Article::setSearchProxy(new Mocks_Model_Article_SearchProxy());
    }

    protected function _injectDisableNavigationCache() 
    {
        Model_Navigation::setUseCache(false);
    }

}
