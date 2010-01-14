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
            
        Shared_XmlRpc::setXmlRpcClientClass('Zend_XmlRpc_Client');
        Mocks_Shared_Project::setLive();
        try {
            $components = $this->_asset->getComponents();
        } catch (Exception $e) {
            $failure = true;
        }
        Mocks_Shared_Project::setTest();
        Shared_XmlRpc::setXmlRpcClientClass('Mocks_Shared_XmlRpc');
        
        if (isset($failure))
            $this->fail("Failed to get components: " . $e->getMessage());
        
        $this->assertTrue(count($components) > 0, 'No components in Trac, why?');
    }

}