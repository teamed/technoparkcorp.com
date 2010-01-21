<?php

require_once 'AbstractTest.php';

class StaticControllerTest extends AbstractTest
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