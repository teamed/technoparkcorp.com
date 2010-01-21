<?php

require_once 'Mocks/artifacts/ProjectRegistry/Project.php';

class Starter extends FaZend_Test_Starter
{

    protected function _startDatabase() 
    {
        // FaZend_Pos_Properties::cleanPosMemory(true);
        // $this->_dropDatabase();
    }
        
}