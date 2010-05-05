<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $this->assert->disc->file
            ->tailf(APPLICATION_PATH . '/../../tpc2.log');
    }

}