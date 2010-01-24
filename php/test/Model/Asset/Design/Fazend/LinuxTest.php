<?php

require_once 'AbstractProjectTest.php';

class Model_Asset_Design_Fazend_LinuxTest extends AbstractProjectTest 
{

    public function setUp()
    {
        parent::setUp();
        $this->_asset = $this->_project->fzProject()->getAsset(Model_Project::ASSET_DESIGN);
        
        $this->assertTrue($this->_asset instanceof Model_Asset_Design_Abstract);
    }

    public function testWeCanRetrieveAllComponents() 
    {
        $components = $this->_asset->getComponents();
        $this->assertTrue(count($components) > 0, 'No components in Trac, why?');
    }
    
    public function testRealLifeCallWorks() 
    {
        if (!defined('TEST_REAL_CONNECTIONS'))
            return $this->markTestIncomplete();
            
        // disabled since "Authorization Required" comes from there :(
        // return $this->markTestIncomplete();
            
        Shared_Pan::setSoapClient(null);
        Mocks_Shared_Soap_Client::setLive();
        try {
            $components = $this->_asset->getComponents();
        } catch (Shared_Pan_SoapFailure $e) {
            $httpClient = Shared_Pan::getSoapClient()->getHttpClient();
            
            logg(
                'connected to "%s", exception raised (%s): "%s"',
                $httpClient->getUri(),
                get_class($e),
                $e->getMessage()
            );
            FaZend_Log::err("Failed to get components");
            $incomplete = true;
        }
        Mocks_Shared_Soap_Client::setTest();
        Shared_Pan::setSoapClient(Mocks_Shared_Pan_SoapClient::get());
        
        if (isset($incomplete)) {
            bug(444);
            $this->markTestIncomplete();
        }

        $this->assertTrue(count($components) > 0, 'No components in Trac, why?');
    }

}