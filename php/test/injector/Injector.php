<?php

require_once 'Mocks/artifacts/ProjectRegistry/Project.php';

class Injector extends FaZend_Test_Injector 
{
    
    protected function _injectTestLogger() 
    {
        // log errors in ALL environments
        // ...
    }
    
    protected function _injectSoapClient() 
    {
        // this class will catch all calls to fazend
        require_once 'Shared/Soap/Gateway.php';
        Shared_Soap_Gateway::setSoapClient(Mocks_Shared_Soap_Client::get());
        
        // no cache, since there are NO real SOAP calls to FaZend
        Shared_Soap_Gateway::setCacheEnabled(false);
    }
        
    protected function _injectMiscellaneous() 
    {
        // clean Shared cache, if necessary
        // Shared_Cache::getInstance('Shared_SOAP_Gateway')->clean();

        // set it to FALSE and unit tests WON'T try to test any real life calls
        define('TEST_REAL_CONNECTIONS', true);

        // don't go into real Shared resources
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        Shared_Trac::setTicketClass('Mocks_Shared_Trac_Ticket');

        // disable file moving after uploading
        Model_Artifact_Attachments::setLocation(false);

        // just to try the translation
        Zend_Registry::get('Zend_Translate')->setLocale(new Zend_Locale('ru'));
    }

    protected function _injectLuceneConfig() 
    {
        // we should set this path to a writable directory
        $path = TEMP_PATH . '/panel2lucene.' . APPLICATION_ENV;
        Model_Article::setLucenePath($path);
    }

    protected function _injectTesterIsLoggedIn() 
    {
        // in testing environment you do EVERYTHING under this role
        // in order to avoid conflicts with real documents in
        // real environment (fazend for example)
        Model_User::setSession(new FaZend_StdObject());
        Model_User::logIn(Mocks_Model_Project::PM);
    }

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
