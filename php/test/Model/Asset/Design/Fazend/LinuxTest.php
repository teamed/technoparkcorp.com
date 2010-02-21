<?php
/**
 * @version $Id$
 */

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
        $this->assertTrue(count($components) > 0, 'No components in Design, why?');
    }
    
    public function testRealLifeCallWorks() 
    {
        Shared_Pan::setSoapClient(null);
        Mocks_Shared_Soap_Client::setLive();
        
        try {
            $pan = new Shared_Pan($this->_project->fzProject());
            $components = $pan->getComponents();
        } catch (Shared_Pan_SoapFailure $e) {
            logg(
                'connected to "%s", exception raised (%s): "%s"',
                '???', //$httpClient->getUri(),
                get_class($e),
                $e->getMessage()
            );
            FaZend_Log::err("Failed to get components");
            $incomplete = true;
        } catch (Exception $e) {
            FaZend_Log::err("Failed to get components: {$e->getMessage()}");
            $incomplete = true;
        }
        
        Mocks_Shared_Soap_Client::setTest();
        Shared_Pan::setSoapClient(Mocks_Shared_Pan_SoapClient::get());
        
        if (isset($incomplete)) {
            $this->markTestIncomplete();
        }

        logg('Received from LINUX: ' . $components);
        // $this->assertTrue(is_array($components), 'No components in design, why?');
    }

}