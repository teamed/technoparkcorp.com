<?php

require_once 'FaZend/Test/TestCase.php';

class PagesTest extends FaZend_Test_TestCase {

    public function setUp() {
        parent::setUp();
        $this->pages = Model_Pages::getInstance();
    }
    
    public function testParsingWorks() {
        $acl = $this->pages->getAcl();
    }

    public function testNavigationWorks() {
        $html = (string)$this->view->navigation()
            ->setContainer($this->pages)
            ->setAcl($this->pages->getAcl());
    }

}