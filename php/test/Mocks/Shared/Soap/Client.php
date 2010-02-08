<?php

class Mocks_Shared_Soap_Client
{
    
    const OWNER = 'yegor256@yahoo.com';
    const OWNER_PWD = 'aF41Atlz';
    
    const PM = 'tester@tpc2.com';
    const PM_PWD = 'violetta';
    
    const WORKER = 'worker@tpc2.com';
    const WORKER_PWD = 'victory';
    
    protected static $_userId = 1;
    
    public static function get() 
    {
        return new self();
    }
    
    public function getProjects() 
    {
        $authz =
            '[' . Mocks_Model_Project::NAME . ":/]\n" . 
            self::PM . " = rw\n" .
            Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = rw\n" .
            self::OWNER . " = rw\n";

        foreach (array(self::PM, self::OWNER, self::WORKER) as $email) {
            foreach (array('PM', 'SystemAnalyst', 'Architect', 'CCB', 'Programmer') as $role) {
                $authz .= '[' . Mocks_Model_Project::NAME . ':' . 
                    Model_Project::ROLE_AUTHZ_PREFIX . "$role]\n" .
                    $email . " = rw\n";
            }
        }

        return array(
            FaZend_StdObject::create()
                ->set('id', Mocks_Model_Project::ID)
                ->set('name', Mocks_Model_Project::NAME)
                ->set('user', self::$_userId)
                ->set('authz', $authz)
                ->set('passwd', 
                    "[users]\n" . 
                    self::PM . " = " . self::PM_PWD . "\n" .
                    Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = test\n" .
                    self::OWNER . ' = ' . self::OWNER_PWD . "\n"
                )
                ->set('tracIni', ''),
            FaZend_StdObject::create()
                ->set('id', 2)
                ->set('name', 'PMO')
                ->set('user', self::$_userId)
                ->set('authz', "[PMO:/]\n" . self::PM . " = rw\n")
                ->set('passwd', "[users]\n" . self::PM . " = " . self::PM_PWD . "\n")
                ->set('tracIni', ''),
            );
    }

    public function getUsers() 
    {
        return array(
            FaZend_StdObject::create()
                ->set('id', 1)
                ->set('email', self::PM)
                ->set('password', self::PM_PWD),
            FaZend_StdObject::create()
                ->set('id', 2)
                ->set('email', self::OWNER)
                ->set('password', self::OWNER_PWD),
        );
    }

    public static function setLive() 
    {
        self::$_userId = 2;
    }

    public static function setTest() 
    {
        self::$_userId = 1;
    }

}

