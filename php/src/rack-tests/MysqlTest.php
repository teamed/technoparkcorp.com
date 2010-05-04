<?php
/**
 * @version $Id$
 */

class MysqlTest extends PhpRack_Test
{

    protected function _init()
    {
        $this->setAjaxOptions(
            array(
                'reload' => 1, // every 1 second, if possible
            )
        );
    }

    public function testConnections()
    {
        $ini = parse_ini_file(APPLICATION_PATH . '/config/app.ini', true);
        $production = $ini['production : global'];
        $this->assert->db->mysql
            ->connect(
                'localhost',
                3306,
                $production['resources.db.params.username'],
                $production['resources.db.params.password']
            )
            ->dbExists($production['resources.db.params.dbname']);
    }

}   
