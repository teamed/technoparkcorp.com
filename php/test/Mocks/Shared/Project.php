<?php

class Mocks_Shared_Project extends Shared_Project
{
    
    const NAME = 'test';

    /**
     * @var Mocks_Shared_Project
     */
    protected static $_project;

    /**
     * @return Shared_Project
     **/
    public static function get() 
    {
        if (isset(self::$_project))
            return self::$_project;
            
        $user = Mocks_Shared_User::get();
        return self::$_project = Shared_Project::create(1, self::NAME, $user, 
            // authz
            '[' . self::NAME. ":/]\n" . 
            "{$user->email} = rw\n" . 
            "yegor256@yahoo.com = rw\n", 
            // passwd
            "[users]\n" . 
            "{$user->email} = hUjfh9Kjfl0K\n" . 
            "yegor256@yahoo.com = aF41Atlz\n", 
            // trac.ini
            '');
    }
    
    public static function setLive() 
    {
        self::$_project->user = Mocks_Shared_User::getLive();
    }

    public static function setTest() 
    {
        self::$_project->user = Mocks_Shared_User::get();
    }

}

