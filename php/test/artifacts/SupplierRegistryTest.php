<?php

require_once 'FaZend/Test/TestCase.php';

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