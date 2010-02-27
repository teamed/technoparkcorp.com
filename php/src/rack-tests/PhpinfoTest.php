<?php
/**
 * @version $Id: EnvironmentTest.php 746 2010-02-24 07:25:28Z yegor256@yahoo.com $
 */

class PhpinfoTest extends PhpRack_Test
{

    public function testPhpinfo()
    {
        $this->assert->php->phpinfo();
    }

}