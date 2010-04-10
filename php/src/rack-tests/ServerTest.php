<?php
/**
 * @version $Id$
 */

class ServerTest extends PhpRack_Test
{

    public function setUp()
    {
        parent::setUp();
        $this->setAjaxOptions(
            array(
                'reload' => 5, // every 5 seconds, if possible
            )
        );
    }

    public function testUptime()
    {
        $this->_log('uptime: ' . shell_exec('uptime'));
    }

    public function testShowProcesses()
    {
        $cmd = 'ps -o "%cpu %mem nice user time stat command" -ax | ' .
        'awk \'NR==1; NR > 1 {print $0 | "sort -k 1 -r"}\' | ' .
        'grep -v "^ 0.0"';
        $this->_log('$ ' . $cmd);
        $this->_log(shell_exec($cmd));
    }

}   