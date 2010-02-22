<?php

class EnvironmentTest extends PhpRack_Test
{

    public function testSvnExists()
    {
    }

    public function testPhpIsConfigured()
    {
        $this->assert->php->version->atLeast('5.2');
        $this->assert->php->extensions
            ->isLoaded('finfo')
            ->isLoaded('simplexml')
            ->isLoaded('xsl');

        $this->assert->php->extensions->finfo->isAlive();
    }

}