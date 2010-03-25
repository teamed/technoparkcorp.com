<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $file = APPLICATION_PATH . '/../../log/php_errors.log';
        $this->assert->disc->file
            ->exists($file)
            ->tail($file, 50);
    }

}