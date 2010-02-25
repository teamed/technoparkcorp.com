<?php
/**
 * @version $Id$
 */

class LinuxServerTest extends PhpRack_Test
{

    public function testServerIsAccessible()
    {
        $this->assert->network->ports->isOpen(80, 'linux.fazend.com');
    }

}