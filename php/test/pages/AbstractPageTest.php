<?php

require_once 'FaZend/Test/TestCase.php';

abstract class AbstractPageTest extends FaZend_Test_TestCase
{

    public function dispatchPage($doc) 
    {
        $this->dispatch($this->view->panelUrl($doc));
    }

}