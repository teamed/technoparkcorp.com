<?php

require_once 'pages/AbstractPageTest.php';

class pages_wobots_AnyPageTest extends AbstractPageTest
{

    public function testListOfSuppliersIsVisible() 
    {
        $this->dispatchPage('wobots/PM.'. Mocks_Model_Project::NAME);
        // $this->assertQuery('table');
    }

}