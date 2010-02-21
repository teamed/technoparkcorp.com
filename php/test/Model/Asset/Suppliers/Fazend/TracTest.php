<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Model_Asset_Suppliers_Fazend_TracTest extends AbstractTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = Model_Project::findByName('PMO');
        $this->_asset = $project->getAsset(Model_Project::ASSET_SUPPLIERS);
    }

    public function testRetrieveAllWorks() 
    {
        $emails = $this->_asset->retrieveAll();
        $this->assertTrue(count($emails) > 0, 'No emails in PMO trac??');
    }
    
    public function testAttributesCanBeDerived() 
    {
        $emails = $this->_asset->retrieveAll();
        $email = array_shift($emails);
        
        $supplier = new theSupplier($email);
        
        $this->_asset->deriveByEmail($email, $supplier);
    }
    
    public function testRealLifeDatabaseOfSuppliersIsAccessible() 
    {
        Shared_XmlRpc::setXmlRpcClientClass('Zend_XmlRpc_Client');
        Model_Asset_Defects_Fazend_Trac::setTicketsPerPage(100);
        try {
            $project = Model_Project::findByName('PMO');
            $asset = $project->getAsset(Model_Project::ASSET_SUPPLIERS);
            $emails = $asset->retrieveAll();
            $email = array_shift($emails);
            $supplier = new theSupplier($email);
            $asset->deriveByEmail($email, $supplier);
        } catch (Shared_Trac_SoapFault $e) {
            FaZend_Log::err(
                sprintf(
                    "Failed to get tickets from Trac (%s): %s", 
                    get_class($e),
                    $e->getMessage()
                )
            );
            $incomplete = true;
        } catch (Exception $e) {
            FaZend_Log::err("Failed to get components: {$e->getMessage()}");
            $incomplete = true;
        }
        Model_Asset_Defects_Fazend_Trac::setTicketsPerPage(3);
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        
        if (isset($incomplete))
            $this->markTestIncomplete();
            
        $this->assertTrue($supplier->rate instanceof FaZend_Bo_Money);
        $this->assertTrue($supplier->approvedOn instanceof Zend_Date);
    }

}