<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    const FILENAME = '/home/beta/log/php_errors.log';

    public function testFileExists()
    {
        if (!file_exists(self::FILENAME)) {
            $this->fail('Log file ' . self::FILENAME . ' is not found');
        }
    }

    public function testShowLogFile()
    {
        $this->log(shell_exec('tail -50 ' . escapeshellarg(self::FILENAME)));
    }

}