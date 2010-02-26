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
 * Panel pages
 *
 * @package Controllers
 */
class PanelController extends FaZend_Controller_Action
{

    /**
     * Pre-configuration
     *
     * @return void
     */
    public function preDispatch()
    {
        // if the user is not logged in - try to log him in
        if (!Model_User::isLoggedIn()) {
            logg('login attempt...');
            // show as much information as possible
            $adapter = new Model_Auth_Adapter(
                array(
                    'accept_schemes' => 'basic',
                    'realm' => 'thePanel 2.0 beta'
                )
            );

            $adapter->setBasicResolver(new Model_Auth_Resolver());
            $adapter->setRequest($this->getRequest());
            $adapter->setResponse($this->getResponse());

            $result = $adapter->authenticate();
            if (!$result->isValid()) {
                logg(
                    'Invalid login attempt (code: %d, identity: %s). %s',
                    $result->getCode(),
                    serialize($result->getIdentity()),
                    implode('; ', $result->getMessages())
                );
                return $this->_forward(
                    'index', // action
                    'static', // controller
                    null, // module, use default
                    array('page' => 'system/404') // params
                );
            }

            $identity = $result->getIdentity();
            $identity = $identity['username'];
            Model_User::logIn($identity);
        }

        // change layout of the view
        Zend_Layout::getMvcInstance()->setLayout('panel');

        // get pages instance for the controller to user later
        $this->_pages = Model_Pages::getInstance();
        return null;
    }

    /**
     * Default and the only action for this controller
     *
     * @return void
     */
    public function indexAction() 
    {
        $doc = $this->_getParam('doc');

        // permission check for current user
        if (!$this->_pages->isAllowed($doc)) {
            return $this->_restrict(_t('Sorry, the document "%s" is not available for you', $doc));
        }
        
        try {
            $this->_buildDocument($doc);
        } catch (Model_Pages_DocumentNotFound $e) {
            return $this->_restrict(
                _t(
                    'Sorry, the document "%s" is not found: %s',
                    $doc,
                    $e->getMessage()
                )
            );
        }
        return null;
    }

    /**
     * Access restricted
     *
     * @return void
     */
    public function restrictAction() 
    {
        $this->view->message = ($this->_hasParam('msg') ? 
            $this->_getParam('msg') : false);
    }

    /**
     * Get list of options for header
     *
     * @return void
     */
    public function optsAction() 
    {
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
    public function redirectorAction() 
    {
        $doc = $this->getRequest()->getPost('document');
        $this->_helper->redirector->gotoRoute(array('doc'=>$doc), 'panel', true, false);
    }

    /**
     * Grant access to shared documents
     *
     * @return void
     */
    public function sharedAction() 
    {
        try {
            $shortcut = Model_Shortcut::findByHash($this->_getParam('doc'));
        } catch (Model_Shortcut_NotFoundException $e) {
            return $this->_restrict(_t('The link you are using is not valid any more'));
        }

        // access control
        if (!in_array(Model_User::getCurrentUser()->email, $shortcut->getEmails())) {
            return $this->_restrict(
                _t(
                    'Sorry, this document "%s" is not shared with you, but only with %s',
                    $shortcut->document,
                    implode(', ', $shortcut->getEmails())
                )
            );
        }
        
        // build document and show it
        $this->_buildDocument($shortcut->document, $shortcuts->getParams());
    }

    /**
     * Build document content
     *
     * @param string Name of the document to render
     * @param array Associative array of params to pass to the view
     * @return string HTML
     **/
    protected function _buildDocument($doc, array $params = array()) 
    {
        $this->view->headTitle($doc . ' -- ');

        try {
            $this->view->document = $this->_pages->buildDocumentHtml($doc, $params);
        } catch (AccessRestrictedException $e) {
            return $this->_restrict($e->getMessage());
        }
    }
    
    /**
     * Restrict access
     *
     * @return void
     **/
    protected function _restrict($message) 
    {
        return $this->_forward(
            'restrict', 
            null, 
            null, 
            array('msg'=>$message)
        );
    }

}
