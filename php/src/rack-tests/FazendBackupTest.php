<?php
/**
 * @version $Id: CodeTest.php 912 2010-04-10 08:19:46Z yegor256@yahoo.com $
 */

class FazendBackupTest extends PhpRack_Test
{

    public function testExecute()
    {
        // execute fazend backup script
        $this->assert->shell->exec(
            'cd ' . escapeshellarg(APPLICATION_PATH . '/../public') . '; env -i php index.php FzBackup'
        );
    }

}