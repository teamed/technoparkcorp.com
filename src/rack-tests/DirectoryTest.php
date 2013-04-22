<?php
/**
 * @version $Id$
 */

class DirectoryTest extends PhpRack_Test
{

    public function testDirectoryTree()
    {
        $this->assert->disc->showDirectory(
            APPLICATION_PATH . '/..',
            array(
                'exclude' => array(
                    '/library\/\w+\/.*/',
                    '/application\/\w+\/.*/',
                    '/content\/\w+\/.*/',
                ),
            )
        );
    }

}