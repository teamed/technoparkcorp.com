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
 * Panel pages
 *
 * @package Controllers
 */
class PanelController extends FaZend_Controller_Action {

    /**
     * Session namespace
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Pre-configuration
     *
     * @return void
     */
    public function preDispatch() {
        
        // in testing environment you do EVERYTHING under this role
        // in order to avoid conflicts with real documents in
        // real environment (fazend for example)
        if (APPLICATION_ENV !== 'production')
            Model_User::logIn('tester@tpc2.com');

        // if the user is not logged in - try to log him/her in
        if (!Model_User::isLoggedIn()) {
            // show as much information as possible
            $adapter = new Model_Auth_Adapter(array(
                'accept_schemes' => 'basic',
                'realm' => 'thePanel 2.0 ' . FaZend_Revision::get() . '/' . count(Model_Project::retrieveAll())));

            $adapter->setBasicResolver(new Model_Auth_Resolver());
            $adapter->setRequest($this->getRequest());
            $adapter->setResponse($this->getResponse());

            $result = $adapter->authenticate();
            if ($result->isValid()) {
                $identity = $result->getIdentity();
                $identity = $identity['username'];
            } else {
                return $this->_forward('index', 'static', null, array('page'=>'system/404'));
            }

            Model_User::logIn($identity);
        }

        // change layout of the view
        Zend_Layout::getMvcInstance()->setLayout('panel');

        // get pages instance for the controller to user later
        $this->_pages = Model_Pages::getInstance();

    }

    /**
     * Default and the only action for this controller
     *
     * @return void
     */
    public function indexAction() {

        $view = clone $this->view;

        $doc = $view->doc = $this->view->doc = $this->_getParam('doc');

        // permission check for current user
        if (!$this->_pages->isAllowed($doc))
            return $this->_forward('restrict', null, null, array('msg'=>'Sorry, the document "' . $doc . '" is not available for you'));

        // configure it, set the active document for further references
        $this->_pages->setActiveDocument($doc);

        $this->view->headTitle($doc . ' -- ' );

        // convert document name into absolute PATH
        $scripts = array();
        $path = $this->_pages->resolvePath($doc, $scripts);

        /**
         *  @todo this should be improved
         */
        $this->view->document = '';
        foreach ($scripts as $script) {
            $view->addScriptPath(dirname($script));
            $this->view->document .= $view->render(pathinfo($script, PATHINFO_BASENAME));
        }

        // reconfigure VIEW in order to render this particular document file
        $view->addScriptPath(dirname($path));
        $this->view->document .= $view->render(pathinfo($path, PATHINFO_BASENAME));

        // if execution inside this view is completed - show only the result
        if ($view->formaCompleted)
            $this->view->document = '<pre class="log">' . $view->formaCompleted . '</pre>';

    }

    /**
     * Access restricted
     *
     * @return void
     */
    public function restrictAction() {
        $this->view->message = ($this->_hasParam('msg') ? 
            $this->_getParam('msg') : false);
    }

    /**
     * Get list of options for header
     *
     * @return void
     */
    public function optsAction() {

        $title = $this->getRequest()->getPost('title');

        // this is required in order to INIT the list of pages
        $this->_pages->setActiveDocument($title);

        $current = $this->_pages->findBy('title', $title);
        $list = array();

        if ($current) {

            foreach ($current->parent->getPages() as $page) {
                if (!$this->_pages->isAllowed($page->resource))
                    continue;
                $list[$page->title] = $page->label;
            }
        }

        $this->_returnJSON($list);

    }

    /**
     * Redirect to document from POST
     *
     * @return void
     */
    public function redirectorAction() {

        $doc = $this->getRequest()->getPost('document');
        $this->_helper->redirector->gotoRoute(array('doc'=>$doc), 'panel', true, false);

    }

}
