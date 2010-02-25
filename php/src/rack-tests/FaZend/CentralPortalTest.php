<?php
/**
 * @version $Id$
 */

class CentralPortalTest extends PhpRack_Test
{

    public function testServerIsAccessible()
    {
        $this->assert->network->ports->isOpen(80, 'www.fazend.com');
    }

}