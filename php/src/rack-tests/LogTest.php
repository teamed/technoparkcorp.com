<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $this->assert->disc->file
            ->tailf(APPLICATION_PATH . '/../../log/php_errors.log');
    }

}