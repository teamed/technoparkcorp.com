<?php

require_once 'FaZend/Test/TestCase.php';

class Model_Asset_Design_Fazend_LinuxTest extends AbstractProjectTest 
{

    public function setUp()
    {
        $this->_asset = $this->_project->fzProject()->getAsset(Model_Project::ASSET_DESIGN);
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
        return $this->markTestIncomplete();
            
        Shared_XmlRpc::setXmlRpcClientClass('Zend_XmlRpc_Client');
        Mocks_Shared_Project::setLive();
        try {
            $components = $this->_asset->getComponents();
        } catch (Exception $e) {
            logg("Failed to get components: " . $e->getMessage());
            $incomplete = true;
        }
        Mocks_Shared_Project::setTest();
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        
        if (isset($incomplete))
            $this->markTestIncomplete();

        $this->assertTrue(count($components) > 0, 'No components in Trac, why?');
    }

}