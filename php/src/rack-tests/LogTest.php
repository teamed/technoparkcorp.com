<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $this->assert->disc->file
            ->tail(APPLICATION_PATH . '/../../log/php_errors.log', 50);
    }

}