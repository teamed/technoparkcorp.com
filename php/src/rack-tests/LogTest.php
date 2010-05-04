<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $file = APPLICATION_PATH . '/../../tpc2.log';
        $this->assert->disc->file
            ->isReadable($file)
            ->isWritable($file)
            ->tailf($file);
    }

}