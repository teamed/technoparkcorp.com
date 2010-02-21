<?php
/**
 * @version $Id$
 */

require_once 'pages/AbstractPageTest.php';

class pages_CoverageOfVisibilityTest extends AbstractPageTest
{

    public static function providerPageNames()
    {
        $name = Mocks_Model_Project::get()->name;
        $pages = array(
            "projects/$name/Integration/Metrics",
            
            // PMO
            'PMO/Suppliers',
            'PMO/StaffRequests',
        );
        return array_map(
            create_function('$a', 'return array($a);'),
            $pages
        );
    }

    /**
     * @dataProvider providerPageNames
     */
    public function testDocumentIsRendered($name) 
    {
        $this->dispatchPage($name);
        $this->assertNotRedirect();
        // bug($this->response->getBody());
        logg('Page %s dispatched', $name);
    }

}