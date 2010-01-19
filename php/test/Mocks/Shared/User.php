<?php

class Mocks_Shared_User extends Shared_User 
{
    
    /**
     * @var Mocks_Shared_User
     */
    protected static $_user;

    /**
     * @return Shared_User
     **/
    public static function get() 
    {
        if (isset(self::$_user))
            return self::$_user;
            
        return self::$_user = Shared_User::create(1, 'test@example.com', 'pwd');
    }

    /**
     * @return Shared_User
     **/
    public static function getLive() 
    {
        return Mocks_Shared_User::create(2, 'yegor256@yahoo.com', 'violetta');
    }

}

