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
 * Static controller (static pages)
 *
 * @package Controllers
 */
class StaticController extends FaZend_Controller_Action {

    /**
     * Default and the only action for this controller
     *
     * @return void
     */
    public function indexAction() {

        try {
            // try to create an article with the give PAGE address
            $article = $this->view->article = Model_Article::createByLabel($this->_getParam('page'));
        } catch (Model_Article_NotFound $e) {
            return $this->_redirect('system/404');
        }    

        // change View script, if this is a home page (/home)
        if ($article->page === 'home') {
            $this->_helper->viewRenderer('home');
            return;
        }    

        Model_Navigation::populateNavigation($this->view->navigation(), $article->page);

        // change content if the PHTML script found    
        //$script = APPLICATION_PATH . '/views/scripts/static' . $xmlFile . '.phtml';
        //if (!file_exists($script) && !preg_match('/\/intro$/', $xmlFile))
        //    $script = preg_replace('/\/(\w[\w\-]+)\.phtml$/', '/_any.phtml', $script);
        //if (file_exists($script)) {
        //    ob_start();
        //    include $script;
        //    $this->view->content = ob_get_clean();
        //}

        // parse special XML meta symbols, like ${url:about/news}
        //$this->view->content = XMLDocument::parseText($this->view->content);
    }

}
