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

/**
 * Model_Asset_Defects_Fazend_Trac test
 *
 * @package test
 */
class Model_Asset_Defects_Fazend_TracTest extends AbstractProjectTest 
{

    public function setUp() 
    {
        parent::setUp();
        $project = Mocks_Model_Project::getInstance();
        $this->_asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
    }

    public function testFindByIdWorks() 
    {
        $ticket = $this->_asset->findById(1);
        $this->assertTrue($ticket->getId() = 1, 'Ticket was not found, why?');
    }

    public function testRetrieveByWorks() 
    {
        $tickets = $this->_asset->retrieveBy(array('id'=>1));
        $this->assertTrue(count($tickets) > 0, 'No ticket were found, why?');
    }

}
