<?php

require_once 'AbstractTest.php';

class Sheet_ROM_Estimate_EstimateAbstractTest extends AbstractTest
{
    
    public function setUp()
    {
        parent::setUp();
        $lines = explode(
            "\n",
            "
                method: three points
                F1: wc=90, bc=10, ml=20
            "
        );
        $this->_tp = Sheet_ROM_Estimate_Abstract::factory($lines);
    }
    
    public function testClassIsSerializable()
    {
        $serialized = serialize($this->_tp);
        $tp2 = unserialize($serialized);
        $this->assertStringEquals(serialize($tp2), $serialized);
    }

}