<?php

require_once 'AbstractTest.php';

class PagesTest extends AbstractTest
{

    public function setUp()
    {
        parent::setUp();
        $this->pages = Model_Pages::getInstance();
    }
    
    public function testParsingWorks()
    {
        $acl = $this->pages->getAcl();
    }

    public function testNavigationWorks()
    {
        $html = (string)$this->view->navigation()
            ->setContainer($this->pages)
            ->setAcl($this->pages->getAcl());
    }

}