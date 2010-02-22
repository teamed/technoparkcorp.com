<?php
/**
 * @version $Id$
 */

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
    
    protected function _injectSharedMocks()
    {
        // clean Shared cache, if necessary
        // Shared_Cache::getInstance('Shared_SOAP_Gateway')->clean();

        // don't go into real Shared resources
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        Shared_Trac::setTicketClass('Mocks_Shared_Trac_Ticket');
        Shared_Pan::setSoapClient(Mocks_Shared_Pan_SoapClient::get());
        
        // make sure we are getting just small number of test tickets
        Model_Asset_Defects_Fazend_Trac::setTicketsPerPage(3);

        // instruct them to logg all events
        Shared_Trac::setVerbose(true);
        Shared_Wiki::setVerbose(true);
    }
        
    protected function _injectMiscellaneous() 
    {
        // disable file moving after uploading
        Model_Artifact_Attachments::setLocation(false);

        // just to try the translation
        Zend_Registry::get('Zend_Translate')->setLocale(new Zend_Locale('ru'));
    }
    
    protected function _injectWeakValidation()
    {
        require_once 'artifacts/ProjectRegistry/Project/Scope/Deliverables/loaders/Issues.php';
        DeliverablesLoaders_Issues::setStrictValidation(false);
    }

    protected function _injectSearchProxy() 
    {
        Model_Article::setSearchProxy(new Mocks_Model_Article_SearchProxy());
    }

    protected function _injectTesterIsLoggedIn() 
    {
        // in testing environment you do EVERYTHING under this role
        // in order to avoid conflicts with real documents in
        // real environment (fazend for example)
        Model_User::setSession(new FaZend_StdObject());
        $pms = Mocks_Model_Project::get()->getStakeholdersByRole('PM');
        Model_User::logIn(array_shift($pms));
    }

    protected function _injectTestProject() 
    {
        // disable any activities with any LIVE projects
        Model_Project::setWeAreManaging(false);

        // add it to registry
        theProjectRegistry::addExtra(Mocks_Model_Project::NAME, new Mocks_theProject());

        // if (defined('CLI_ENVIRONMENT')) {
        //     $project = Model_Artifact::root()->projectRegistry[Mocks_Model_Project::NAME];
        //     foreach ($project->ps()->properties as $property) {
        //         if (!isset($project->$property))
        //             continue;
        //         if (!($project->$property instanceof Model_Artifact_Passive))
        //             continue;
        //         $project->$property->reload();
        //     }
        // }
    }

    protected function _injectAccessRights() 
    {
        // initialize ACL
        $acl = Model_Pages::getInstance()->getAcl();
        
        // add this role to ACL
        $email = Model_User::getCurrentUser()->email;
        if (!$acl->hasRole($email))
            $acl->addRole($email);
        
        // give access to everything for the testing user
        $acl->allow($email);
    }

}
