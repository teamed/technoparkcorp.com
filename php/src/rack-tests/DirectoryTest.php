<?php
/**
 * @version $Id: EnvironmentTest.php 746 2010-02-24 07:25:28Z yegor256@yahoo.com $
 */

class DirectoryTest extends PhpRack_Test
{

    public function testDirectoryTree()
    {
        $this->assert->disc->showDirectory(
            '../../',
            array(
                'exclude' => array(
                    '/library\/\w+\/.*/',
                    '/application\/\w+\/.*/',
                ),
            )
        );
    }

}