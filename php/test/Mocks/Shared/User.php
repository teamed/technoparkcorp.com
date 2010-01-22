<?php

class Mocks_Shared_User extends Shared_User 
{
    
    /**
     * @return Shared_User
     * @see Mocks_Shared_Soap_Client
     **/
    public static function get() 
    {
        return Shared_User::findByEmail(Mocks_Shared_Soap_Client::PM);
    }

}

