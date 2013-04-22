<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class StaticControllerTest extends AbstractTest
{

    public function testHomePageIsAccessible() 
    {
        $this->dispatch('/');
        $this->assertController('static', $this->getResponse()->getBody());
        $this->assertAction('index', $this->getResponse()->getBody());
    }

    public function testContentPageIsAccessible() 
    {
        $this->dispatch('/process/cost');
        $this->assertController('static', $this->getResponse()->getBody());
        $this->assertAction('index', $this->getResponse()->getBody());
    }

}