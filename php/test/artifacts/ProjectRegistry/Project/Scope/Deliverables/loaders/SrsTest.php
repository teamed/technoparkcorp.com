<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class Deliverables_loaders_SrsTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $this->_loader = DeliverablesLoaders_Abstract::factory(
            'srs',
            $this->_project->deliverables
        );
    }

    public function testLoadingWorks()
    {
        $d = $this->_project->deliverables;
        $d->ps()->cleanArray();
        $this->_loader->load();
        
        foreach (array('actors', 'glossary', 'requirements') as $shortcut) {
            $this->assertTrue(
                count($d->{$shortcut}) > 0, 
                "{$shortcut} is empty, why?"
            );
        }
    }

}