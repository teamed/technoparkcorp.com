<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class ShortcutTest extends AbstractProjectTest 
{

    public function testMechanismWorks() 
    {
        $shortcut = Model_Shortcut::create(
            'projects/test/Scope', 
            array('test@example.com'), 
            array('alpha'=>'beta'), 
            true);
            
        $hash = $shortcut->getHash();
        $shortcut1 = Model_Shortcut::findByHash($hash);
        logg('Hash: ' . $hash);
        
        // $this->assertEquals($shortcut1->getHash(), $hash);
    }

}