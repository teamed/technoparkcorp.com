<?php

require_once 'AbstractTest.php';

class Sheet_SheetAbstractTest extends AbstractTest
{

    public function testFactoryMethodWorks()
    {
        $this->assertTrue(Sheet_Abstract::isValidName('Vision'));
    }

}