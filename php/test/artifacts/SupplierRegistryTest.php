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
        FaZend_Pos_Properties::cleanPosMemory();
        $this->assertTrue(count(Model_Artifact::root()->supplierRegistry) > 0, 
            'SupplierRegistry is empty again, why?');
    }

    public function testStaffRequestCanBeResolved() 
    {
        $requests = Model_Artifact::root()->projectRegistry->getStaffRequests();
        foreach ($requests as $request) {
            $response = Model_Artifact::root()->supplierRegistry->resolve($request);
            foreach ($response as $item) {
                $this->assertTrue($item->supplier instanceof theSupplier, 
                    "Invalid SUPPLIER in response: " . gettype($item->supplier));
                $this->assertTrue(is_integer($item->quality),
                    "Invalid QUALITY in response: " . gettype($item->quality));
                $this->assertTrue(is_string($item->reason),
                    "Invalid REASON in response: " . gettype($item->reason));
            }
        }
    }
    
}