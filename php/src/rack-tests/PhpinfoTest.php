<?php
/**
 * @version $Id$
 */

class PhpinfoTest extends PhpRack_Test
{

    public function testPhpIni()
    {
        $this->assert->php->ini('short_open_tag');
    }

    public function testPhpinfo()
    {
        // show full phpinfo() listing
        $this->assert->php->phpinfo();
    }

}