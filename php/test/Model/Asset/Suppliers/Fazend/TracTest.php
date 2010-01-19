<?php

require_once 'FaZend/Test/TestCase.php';

class Model_Asset_Suppliers_Fazend_TracTest extends AbstractProjectTest 
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

}