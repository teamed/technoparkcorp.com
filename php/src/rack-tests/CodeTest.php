<?php
/**
 * @version $Id$
 */

class CodeTest extends PhpRack_Test
{

    public function testLint()
    {
        $this->_setAjaxOptions(
            array(
                'autoStart' => false, // don't reload it from start
            )
        );
        // show full phpinfo() listing
        $this->assert->php->lint(
            APPLICATION_PATH . '/..',
            array(
                'extensions' => 'php,phtml',
            )
        );
    }

}