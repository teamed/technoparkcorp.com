<?php

require_once 'AbstractTest.php';

class Sheet_ROM_Estimate_ThreePointsTest extends AbstractTest
{
    
    public function testClassConstructionWorks()
    {
        $lines = explode(
            "\n",
            "
                method: three points
                F1: wc=90, bc=10, ml=20
                F2: wc=64, bc=16, ml=24
            "
        );
        $tp = Sheet_ROM_Estimate_Abstract::factory($lines);
        $hours = $tp->hours;
        $this->assertEquals(30 + 29, $hours);
    }

}