<?php

require_once 'AbstractTest.php';

abstract class AbstractPageTest extends AbstractTest
{

    public function dispatchPage($doc) 
    {
        $this->dispatch($this->view->panelUrl($doc));
    }

}