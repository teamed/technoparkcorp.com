<?php

class EnvironmentTest extends PhpRack_Test
{

    public function testSvnExists()
    {
        $this->assert->php->version->atLeast('5.2');
    }

}