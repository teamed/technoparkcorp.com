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
 * Test theSupplierRegistry class
 *
 * @package test
 */
class theSupplierRegistryTest extends FaZend_Test_TestCase
{

    public function testCollectionOfSuppliersWorks() 
    {
        $registry = Model_Artifact::root()->supplierRegistry;
        
        // reload it explicitly
        $registry->reload();
        
        $count = 0;
        foreach ($registry as $supplier) {
            $count++;
            $this->assertTrue(strlen($supplier->email) > 0, 'Email is empty, why?');
        }
        $this->assertTrue($count > 0, 'No suppliers found, why?');
        $this->assertTrue(count(Model_Artifact::root()->supplierRegistry) > 0, 
            'No suppliers found, why?');

        // now we should check that the registry is recoverable
        // from POS
        Model_Artifact::root()->supplierRegistry->ps()->save();
        FaZend_Pos_Abstract::cleanPosMemory();
        $this->assertTrue(count(Model_Artifact::root()->supplierRegistry) > 0, 
            'SupplierRegistry is empty again, why?');
    }

}