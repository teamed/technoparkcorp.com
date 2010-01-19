<?php

require_once 'FaZend/Test/TestCase.php';

class StaticControllerTest extends FaZend_Test_TestCase 
{

    public function testHomePageIsAccessible() 
    {
        $this->dispatch('');
        $this->assertController('static');
        $this->assertAction('index');
    }

    public function testContentPageIsAccessible() 
    {
        $this->dispatch('process/cost');
        $this->assertController('static');
        $this->assertAction('index');
    }

}