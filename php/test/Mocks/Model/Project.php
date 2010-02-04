<?php

class Mocks_Model_Project extends Model_Project 
{

    const NAME = 'test';
    const ID = 1;

    /**
    * @see Mocks_Shared_Soap_Client
     */
    public static function get($id = self::ID) 
    {
        return Model_Project::findById($id);
    }

}
