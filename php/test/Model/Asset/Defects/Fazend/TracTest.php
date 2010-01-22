<?php

require_once 'FaZend/Test/TestCase.php';

class Model_Asset_Defects_Fazend_TracTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = $this->_project->fzProject();
        $this->_asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
    }

    public function testFindByIdWorks() 
    {
        $ticket = $this->_asset->findById(1);
        $this->assertTrue($ticket->id == 1, 'Ticket was not found, why?');
    }

    public function testRetrieveByWorks() 
    {
        $tickets = $this->_asset->retrieveBy(array('id'=>1));
        $this->assertTrue(count($tickets) > 0, 'No ticket were found, why?');
    }

    public function testRealLifeCallWorks() 
    {
        if (!defined('TEST_REAL_CONNECTIONS'))
            return $this->markTestIncomplete();
            
        Shared_XmlRpc::setXmlRpcClientClass('Zend_XmlRpc_Client');
        Mocks_Shared_Soap_Client::setLive();
        try {
            $tickets = $this->_asset->retrieveBy();
        } catch (Exception $e) {
            FaZend_Log::err("Failed to get tickets from Trac: " . $e->getMessage());
            $incomplete = true;
        }
        
        Mocks_Shared_Soap_Client::setTest();
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        
        if (isset($incomplete))
            $this->markTestIncomplete();
            
        $this->assertTrue(count($tickets) > 0, 'No tickets in Trac, why?');
    }

}
