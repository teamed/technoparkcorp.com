<?php
/**
 * @version $Id: PhpinfoTest.php 856 2010-03-25 08:59:11Z yegor256@yahoo.com $
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