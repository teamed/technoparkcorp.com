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
 * This class injects test components into a workable system
 *
 * Bootstrap calls this class only when APPLICATION_ENV is not 'production'
 *
 * @see bootstrap.php
 * @package test
 */
class Injector extends FaZend_Test_Injector 
{

    protected static $_done = false;

    /**
     * Make all injections necessary
     *
     * @return void
     **/
    public function inject() 
    {
        // bug('injector');
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_inject/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
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

        // we should use POS?
        defined('USE_POS') or define('USE_POS', class_exists('FaZend_POS', false));

        // just to try the translation
        Zend_Registry::get('Zend_Translate')->setLocale(new Zend_Locale('ru'));
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

        // initialize root
        Model_Artifact::root();

        require_once 'Mocks/artifacts/ProjectRegistry/Project.php';
        Model_Artifact::root()->projectRegistry->add($p = new Mocks_theProject());            
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
        
        // give access to everything for the testing user
        $acl->allow(Mocks_Model_Project::PM);
    }

    /**
     * Injects a few suppliers to the DB
     *
     * @return void
     **/
    // protected function _injectSuppliers() {
    //     $registry = Model_Artifact::root()->supplierRegistry;
    //     $registry->createSupplier(Mocks_Model_Project::PM, 'Mr John Tester', 'US')
    //         ->createSkill('PHP', 75)
    //         ->createSkill('jQuery', 25)
    //         ->createRole('Programmer', '13EUR');
    // 
    //     $registry->createSupplier('test@example.com', 'Mr Alex Peterson', 'UA')
    //         ->createSkill('PHP', 75)
    //         ->createSkill('XML', 15)
    //         ->createSkill('ZendFramework', 85)
    //         ->createSkill('jQuery', 25)
    //         ->createRole('Programmer', '13EUR');    
    // }

}