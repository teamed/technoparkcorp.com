<?php

require_once 'pages/AbstractPageTest.php';

class pages_projects_AllPagesTest extends AbstractPageTest
{

    public static function providerPageNames()
    {
        $name = Mocks_Model_Project::get()->name;
        return array(
            array("projects/$name/Integration/Metrics"),
        );
    }

    /**
     * @dataProvider providerPageNames
     */
    public function testDocumentIsRendered($name) 
    {
        $this->dispatchPage($name);
        logg('Page %s dispatched, %d bytes', $name, 1);
    }

}