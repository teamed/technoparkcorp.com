<?php
/**
 * @version $Id: EnvironmentTest.php 837 2010-03-16 14:37:24Z yegor256@yahoo.com $
 */

class DatabaseTest extends PhpRack_Test
{

    public function testDatabaseExists()
    {
        $ini = parse_ini_file(APPLICATION_PATH . '/config/app.ini', true);
        $production = $ini['production'];
        $this->assert->db->mysql
            ->connect(
                'localhost',
                3306,
                $production['resources.db.params.username'],
                $production['resources.db.params.password']
            )
            ->dbExists($production['resources.db.params.dbname'])
            ->tableExists('session');
    }

}