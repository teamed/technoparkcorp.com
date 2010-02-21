<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Model_Asset_Opportunities_Fazend_TracTest extends AbstractTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = Model_Project::findByName('Sales');
        $this->_asset = $project->getAsset(Model_Project::ASSET_OPPORTUNITIES);
    }

    public function testRetrieveAllWorks() 
    {
        $ids = $this->_asset->retrieveAll();
        $this->assertEquals(array('BestOffer'), $ids);
    }
    
    public function testObjectCanBeDerived() 
    {
        $ids = $this->_asset->retrieveAll();
        $id = array_shift($ids);
        
        $opp = new theOpportunity($id);
        
        $this->_asset->deriveById($id, $opp);
    }
    
}