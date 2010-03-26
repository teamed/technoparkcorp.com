<?php
/**
 * @version $Id$
 */

class CodeTest extends PhpRack_Test
{

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