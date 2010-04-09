<?php
/**
 * @version $Id$
 */

class Starter extends FaZend_Test_Starter
{

    protected function _startDatabase() 
    {
        $this->_bootstrap('db');
        $this->_dropDatabase();
    }
        
}