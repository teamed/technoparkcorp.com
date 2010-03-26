<?php
/**
 * @version $Id$
 */

class LogTest extends PhpRack_Test
{

    public function testShowLogFile()
    {
        $this->_setAjaxOptions(
            array(
                'reload' => 5, // every 5 seconds, if possible
            )
        );
        $this->assert->disc->file
            ->tail(APPLICATION_PATH . '/../../log/php_errors.log', 50);
    }

}