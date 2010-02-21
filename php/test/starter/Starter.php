<?php
/**
 * @version $Id$
 */

require_once 'Mocks/artifacts/ProjectRegistry/Project.php';

class Starter extends FaZend_Test_Starter
{

    protected function _startDatabase() 
    {
        $this->_dropDatabase();
    }
        
}