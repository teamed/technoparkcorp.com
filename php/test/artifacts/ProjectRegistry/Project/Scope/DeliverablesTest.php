<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class DeliverablesTest extends AbstractProjectTest
{

    public function setUp()
    {
        parent::setUp();
        $this->_deliverables = $this->_project->deliverables;
        $this->_deliverables->reload();
    }

    public function testReloadingOfDeliverablesWorks()
    {
        $this->assertTrue(count($this->_deliverables) > 0, 'empty list of deliverables, why?');
    }

    public function testDeliverablesAreAccessedByShortcuts()
    {
        $shortcuts = array(
            'design',
            'requirements',
            'glossary',
            'classes',
            'actors',
            'functional',
        );
        
        foreach ($shortcuts as $shortcut) {
            $this->assertTrue(
                count($this->_deliverables->{$shortcut}) > 0, 
                "Empty shortcut: Deliverables->{$shortcut}, why?"
            );
        }
    }

    public function testDeliverablesPluginsAreAccessible()
    {
        $plugins = array(
            'queue', // development queue
            'all', // everything
        );
        
        foreach ($plugins as $plugin) {
            $list = $this->_deliverables->{$plugin};
            $this->assertTrue($list instanceof Deliverables_Plugin_Abstract);
        }
    }

}