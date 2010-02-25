<?php
/**
 * @version $Id: EnvironmentTest.php 746 2010-02-24 07:25:28Z yegor256@yahoo.com $
 */

class LogTest extends PhpRack_Test
{

    const FILENAME = '/home/beta/log/php_errors.log';

    public function testFileExists()
    {
        if (!file_exists(FILENAME)) {
            $this->fail('Log file ' . FILENAME . ' is not found');
        }
    }

    public function testShowLogFile()
    {
        $this->log(shell_exec('tail -50 ' . escapeshellarg(FILENAME)));
    }

}