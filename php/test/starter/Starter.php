<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'Mocks/artifacts/ProjectRegistry/Project.php';

/**
 * This class is executed before all tests
 *
 * @package test
 */
class Starter extends FaZend_Test_Starter
{

    /**
     * Delete all tables from the database
     *
     * @return void
     **/
    protected function _startDatabase() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        while (count($db->listTables())) {
            foreach ($db->listTables() as $table) {
                try {
                    $db->query('DROP TABLE ' . $db->quoteIdentifier($table));
                    echo "Table droped: {$table}\n";
                } catch (Exception $e) {
                    // ignore it
                }
            }
        }
    }
        
}