<?php

class Mocks_Model_Project extends Model_Project 
{

    const NAME = 'test';
    const OWNER = 'yegor256@yahoo.com';
    const OWNER_PWD = 'violetta';
    const PM = 'tester@tpc2.com';
    const PM_PWD = 'violetta';

    protected static $_instance;

    public function __construct() 
    {
        $pwd = md5(rand());
        
        $authz = '[' . self::NAME . ":/]\n" . self::PM . " = rw\n";
        foreach (array('PM', 'SystemAnalyst', 'Architect', 'CCB', 'Programmer') as $role)
            $authz .= '[' . self::NAME . ':' . Model_Project::ROLE_AUTHZ_PREFIX . "$role]\n" . self::PM . " = rw\n";
        
        parent::__construct(1, // id
            self::NAME, // project name
            new Shared_User(1, self::OWNER, self::OWNER_PWD), // project owner
            $authz, // authz file
            self::PM . ' = ' . self::PM_PWD . "\n" . 
                self::OWNER . '=' . self::OWNER_PWD . "\n", // passwd file
            false // trac.ini
            );     
    }

    public static function getInstance() 
    {
        if (!isset(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

}
