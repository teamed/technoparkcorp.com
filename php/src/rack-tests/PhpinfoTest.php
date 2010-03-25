<?php
/**
 * @version $Id$
 */

class PhpinfoTest extends PhpRack_Test
{

    public function testPhpinfo()
    {
        // show full phpinfo() listing
        $this->assert->php->phpinfo();
    }

}