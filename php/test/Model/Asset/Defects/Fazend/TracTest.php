<?php

require_once 'AbstractProjectTest.php';

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
    
    public function testRetrieveByCanReturnFullListOfTickets() 
    {
        $tickets = $this->_asset->retrieveBy();
        $this->assertTrue(count($tickets) > 0, 'No ticket were found, why?');
    }
    
    public function testRealLifeCallWorks() 
    {
        Shared_XmlRpc::setXmlRpcClientClass('Zend_XmlRpc_Client');
        Mocks_Shared_Soap_Client::setLive();
        Model_Asset_Defects_Fazend_Trac::setTicketsPerPage(100);
        try {
            $tickets = $this->_asset->retrieveBy();
            $ticketId = current($tickets);
            $ticket = $this->_asset->findById($ticketId);
            $ticket->changelog;
        } catch (Shared_Trac_SoapFault $e) {
            FaZend_Log::err(
                sprintf(
                    "Failed to get tickets from Trac (%s): %s", 
                    get_class($e),
                    $e->getMessage()
                )
            );
            $incomplete = true;
        }
        
        Model_Asset_Defects_Fazend_Trac::setTicketsPerPage(3);
        Mocks_Shared_Soap_Client::setTest();
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        
        if (isset($incomplete))
            $this->markTestIncomplete();
            
        $this->assertTrue(count($tickets) > 0, 'No tickets in Trac, why?');
    }

}
