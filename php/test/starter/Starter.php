<?php
/**
 * @version $Id$
 */

class Starter extends FaZend_Test_Starter
{

    protected function _startDatabase() 
    {
        // bootstrap the database connection
        $this->_bootstrap('db');
        
        // remove all tables and views from the DB.
        $this->_dropDatabase();
    }
        
}