<?php
/**
 * @version $Id: EnvironmentTest.php 746 2010-02-24 07:25:28Z yegor256@yahoo.com $
 */

class CentralPortalTest extends PhpRack_Test
{

    public function testServerIsAccessible()
    {
        $this->assert->network->ports->isOpen(80, 'www.fazend.com');
    }

}