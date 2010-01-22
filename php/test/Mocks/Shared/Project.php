<?php

class Mocks_Shared_Project
{
    
    /**
     * @return Shared_Project
     * @see Mocks_Shared_Soap_Client
     **/
    public static function get() 
    {
        return Shared_Project::findByName(Mocks_Model_Project::NAME);
    }
    
}

