<?php
/**
 * @version $Id$
 */

class EnvironmentTest extends PhpRack_Test
{

    public function testPhpIsConfigured()
    {
        $this->assert->php->version->atLeast('5.2');
        $this->assert->php->extensions
            ->isLoaded('simplexml')
            ->isLoaded('pdo')
            ->isLoaded('json')
            ->isLoaded('mysql')
            ->isLoaded('dom')
            ->isLoaded('pdo_mysql')
            ->isLoaded('xsl');
    }

    public function testShowLogFile()
    {
        $file = APPLICATION_PATH . '/../../tpc2.log';
        $this->assert->disc->file
            ->isReadable($file)
            ->isWritable($file);
    }

}