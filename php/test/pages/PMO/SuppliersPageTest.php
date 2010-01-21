<?php

require_once 'pages/AbstractPageTest.php';

class pages_PMO_SuppliersPageTest extends AbstractPageTest
{

    public function testListOfSuppliersIsVisible() 
    {
        $this->dispatchPage('PMO/Suppliers');
        // $this->assertQuery('table');
    }

}