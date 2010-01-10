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

require_once 'Mocks/artifacts/ProjectRegistry/Project.php';

/**
 * This class injects test components into a workable system
 *
 * Bootstrap calls this class only when APPLICATION_ENV is not 'production'
 *
 * @see bootstrap.php
 * @package test
 */
class Injector extends FaZend_Test_Injector 
{
    
    /**
     * Inject extra logger
     *
     * @return void
     **/
    protected function _injectTestLogger() 
    {
        // log errors in ALL environments
        // ...
    }
    
    /**
     * Make sure we don't do any actual connections to fazend
     *
     * @return void
     **/
    protected function _injectSoapClient() 
    {
        // this class will catch all calls to fazend
        require_once 'Shared/Soap/Gateway.php';
        Shared_Soap_Gateway::setSoapClient(Mocks_Shared_Soap_Client::get());
        
        // no cache, since there are NO real SOAP calls to FaZend
        Shared_Soap_Gateway::setCacheEnabled(false);
    }
        
    /**
     * Minor config options
     *
     * @return void
     **/
    protected function _injectMiscellaneous() 
    {
        // clean Shared cache, if necessary
        // Shared_Cache::getInstance('Shared_SOAP_Gateway')->clean();

        // don't go into real Shared resources
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        Shared_Trac::setTicketClass('Mocks_Shared_Trac_Ticket');

        // disable file moving after uploading
        Model_Artifact_Attachments::setLocation(false);

        // just to try the translation
        Zend_Registry::get('Zend_Translate')->setLocale(new Zend_Locale('ru'));
    }

    /**
     * Configure LUCENE
     *
     * @return void
     **/
    protected function _injectLuceneConfig() 
    {
        // we should set this path to a writable directory
        $path = TEMP_PATH . '/panel2lucene.' . APPLICATION_ENV;
        Model_Article::setLucenePath($path);
    }

    /**
     * Injects a tester logged in
     *
     * @return void
     **/
    protected function _injectTesterLogin() 
    {
        // in testing environment you do EVERYTHING under this role
        // in order to avoid conflicts with real documents in
        // real environment (fazend for example)
        Model_User::setSession(new FaZend_StdObject());
        Model_User::logIn(Mocks_Model_Project::PM);
    }

    /**
     * Injects a test project into application
     *
     * @return void
     **/
    protected function _injectTestProject() 
    {
        // disable any activities with any LIVE projects
        Model_Project::setWeAreManaging(false);

        // add it to registry
        theProjectRegistry::addExtra(Mocks_Model_Project::NAME, new Mocks_theProject());

        if (defined('CLI_ENVIRONMENT')) {
            $project = FaZend_Pos_Abstract::root()->projectRegistry[Mocks_Model_Project::NAME];
            foreach ($project->ps()->properties as $property) {
                if (!isset($project->$property))
                    continue;
                if (!($project->$property instanceof Model_Artifact_Passive))
                    continue;
                $project->$property->reload();
            }
        }
        
        // we need this line because we should SAVE our
        // test project to the POS registry, before any other
        // tests get access to POS
        FaZend_Pos_Abstract::root()->ps()->saveAll();
    }

    /**
     * Tester has rights to access any/all pages
     *
     * @return void
     **/
    protected function _injectAccessRights() 
    {
        // initialize ACL
        $acl = Model_Pages::getInstance()->getAcl();
        
        // add this role to ACL
        if (!$acl->hasRole(Mocks_Model_Project::PM))
            $acl->addRole(Mocks_Model_Project::PM);
        
        // give access to everything for the testing user
        $acl->allow(Mocks_Model_Project::PM);
    }

}
