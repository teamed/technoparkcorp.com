<?php
/**
 * @version $Id$
 */

class CodeTest extends PhpRack_Test
{

    protected function _init()
    {
        $this->setAjaxOptions(
            array(
                'autoStart' => false, // don't reload it from start
            )
        );
    }
    
    public function testLint()
    {
        // show full phpinfo() listing
        $this->assert->php->lint(
            APPLICATION_PATH . '/..',
            array(
                'extensions' => 'php,phtml',
            )
        );
    }

}