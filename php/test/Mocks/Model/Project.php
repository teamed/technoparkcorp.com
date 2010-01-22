<?php

class Mocks_Model_Project extends Model_Project 
{

    const NAME = 'test';

    /**
    * @see Mocks_Shared_Soap_Client
     */
    public static function get() 
    {
        return Model_Project::findByName(self::NAME);
    }

}
