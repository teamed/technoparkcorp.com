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
        
        $f1 = (90 + 10 + 20 * 4) / 6;
        $f2 = (64 + 16 + 24 * 4) / 6;
        
        $this->assertEquals(round(($f1 + $f2) * $tp->multiplier), $hours);
    }

}