<?php
/**
 * @version $Id$
 */

class ServerTest extends PhpRack_Test
{

    protected function _init()
    {
        $this->setAjaxOptions(
            array(
                'reload' => 5, // every 5 seconds, if possible
            )
        );
    }

    public function testShowProcesses()
    {
        $this->assert->shell->exec('uptime');
        $this->assert->shell->exec(
            'ps -o "%cpu %mem nice user time stat command" -ax | '
            . 'awk \'NR==1; NR > 1 {print $0 | "sort -k 1 -r"}\' | '
            . 'grep -v "^ 0.0"'
        );
    }

}   