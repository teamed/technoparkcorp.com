<?php

require_once 'pages/AbstractPageTest.php';

class pages_PMO_Suppliers_requests_RequestPageTest extends AbstractPageTest
{

    public function testStaffRequestIsVisible() 
    {
        $this->dispatchPage('PMO/Suppliers/requests');
    }

}